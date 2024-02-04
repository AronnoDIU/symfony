<?php

// src/Controller/Api/SaleApiController.php

namespace App\Controller\Api;

use App\Entity\Sale;
use App\Repository\SaleRepository;
use App\Repository\StockRepository;
use App\Service\SaleService;
use Exception;
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
    private SaleService $saleService;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        SaleService            $saleService,
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator
    )
    {
        $this->saleService = $saleService;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
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
     * @throws Exception
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sale = $this->serializer->deserialize($request->getContent(), Sale::class, 'json');

        $result = $this->saleService->createSale($sale);

        if (isset($result['error'])) {
            return new JsonResponse(['error' => $result['error']], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Create a SerializationContext
        $context = SerializationContext::create()->setGroups(['sale:read']);

        // Serialize using the context
        $responseData = $this->serializer->serialize($result['sale'], 'json', $context);

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
     * @throws Exception
     */
    public function approve(Sale $sale): JsonResponse
    {
        $result = $this->saleService->approveSale($sale);

        if (isset($result['error'])) {
            return new JsonResponse(['error' => $result['error']], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => $result['message']], JsonResponse::HTTP_OK);
    }
}