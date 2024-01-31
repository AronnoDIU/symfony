<?php

// src/Controller/SaleApiController.php

namespace App\Controller\Api;

use App\Entity\Sale;
use App\Repository\SaleRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/sale")
 */
class SaleApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private StockRepository $stockRepository;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, StockRepository $stockRepository)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @Route("/list", methods={"GET"})
     * @throws ExceptionInterface
     */
    public function list(SaleRepository $saleRepository): JsonResponse
    {
        $sales = $saleRepository->findAll();
        $data = $this->serializer->normalize($sales, null, ['groups' => 'sale:read']);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }


    /**
     * @Route("/show/{id}", methods={"GET"})
     * @throws ExceptionInterface
     */
    public function show(Sale $sale): JsonResponse
    {
        $data = $this->serializer->normalize($sale, null, ['groups' => 'sale:read']);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/create", methods={"POST"})
     * @throws ExceptionInterface
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

        // Normalize and return the response
        $responseData = $this->serializer->normalize($sale, null, ['groups' => 'sale:write']);
        return new JsonResponse($responseData, JsonResponse::HTTP_CREATED);
    }


    /**
     * @Route("/update/{id}", methods={"PUT"})
     * @throws ExceptionInterface
     */
    public function update(Sale $sale, Request $request): JsonResponse
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true);

        $this->serializer->deserialize($request->getContent(), Sale::class, 'json', ['object_to_populate' => $sale]);

        $errors = $this->validator->validate($sale);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $responseData = $this->serializer->normalize($sale, null, ['groups' => 'sale:write']);

        return new JsonResponse($responseData, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function delete(Sale $sale): JsonResponse
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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