<?php

// src/Controller/Api/SaleApiController.php

namespace App\Controller\Api;

use App\Entity\Sale;
use App\Repository\SaleRepository;
use App\Repository\StockRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/sale")
 */
class SaleApiController extends AbstractController
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private StockRepository $stockRepository;

    public function __construct(
        MailerInterface        $mailer,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator,
        StockRepository        $stockRepository)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @Route("/list", methods={"GET"})
     */
    public function list(SaleRepository $saleRepository): JsonResponse
    {
        $sales = $saleRepository->findAll();

        // Debugging: Output the state of related entities
        foreach ($sales as $sale) {
            dump($sale->getProduct(), $sale->getLocation());
        }

        // Create a SerializationContext
        $context = SerializationContext::create()->setGroups(['sale:read']);

        // Serialize using the context
        $data = $this->serializer->serialize($sales, 'json', $context);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }


    /**
     * @Route("/show/{id}", methods={"GET"})
     */
    public function show(Sale $sale): JsonResponse
    {
        // Create a SerializationContext
        $context = SerializationContext::create()->setGroups(['sale:read']);

        // Serialize using the context
        $data = $this->serializer->serialize($sale, 'json', $context);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/create", methods={"POST"})
     * @throws TransportExceptionInterface
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sale = $this->serializer->deserialize($request->getContent(), Sale::class, 'json');

        // Validate the sale
        $errors = $this->validator->validate($sale);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Persist associated entities explicitly
        $this->entityManager->persist($sale->getProduct());
        $this->entityManager->persist($sale->getLocation());

        // Persist the sale
        $this->entityManager->persist($sale);
        $this->entityManager->flush();

        // Update stock quantity after the sale has been persisted
        $sale->getStock()->addSale($sale);
        $this->entityManager->flush();

        // Check if the sale price is greater than 1000
        if ($sale->getPrice() > 1000.00) {
            // Email the customer
            $customerEmail = $sale->getCustomer()->getEmail();
            $email = (new Email())
                ->from('admin@dev.symfony.com')
                ->to($customerEmail)
                ->subject('Sale Notification')
                ->html($this->renderView('emails/email.html.twig', ['customerName' => $sale->getCustomer()->getName()]));

            $this->mailer->send($email);
        }

        // Create a SerializationContext
        $context = SerializationContext::create()->setGroups(['sale:read']);

        // Serialize using the context
        $responseData = $this->serializer->serialize($sale, 'json', $context);

        return new JsonResponse($responseData, JsonResponse::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/update/{id}", methods={"PUT"})
     */
    public function update(Sale $sale, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Create a DeserializationContext
        $context = DeserializationContext::create()->setGroups(['sale:write']);

        $this->serializer->deserialize($request->getContent(), Sale::class, 'json', $context, ['object_to_populate' => $sale]);

        $errors = $this->validator->validate($sale);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        // Create a SerializationContext
        $context = SerializationContext::create()->setGroups(['sale:read']);

        // Serialize using the context
        $responseData = $this->serializer->serialize($sale, 'json', $context);

        return new JsonResponse($responseData, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function delete(Sale $sale): JsonResponse
    {
        $this->entityManager->remove($sale);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/approve/{id}", methods={"POST"})
     * @ParamConverter("sale", class="App\Entity\Sale")
     */
    public function approve(Sale $sale): JsonResponse
    {
        // Check if the sale is already approved
        if ($sale->getStatus() === 'Approve') {
            return new JsonResponse(['message' => 'Sale is already approved.'], JsonResponse::HTTP_OK);
        }

        // Set the status to 'Approve'
        $sale->setStatus('Approve');
        $this->entityManager->flush();

        // Update stock quantity after the sale status has been changed to 'Approve'
        $sale->getStock()->addSale($sale);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Sale approved and added to Stock.'], JsonResponse::HTTP_OK);
    }
}