<?php

// src/Controller/Api/SaleController.php

namespace App\Controller\Api;

use App\Entity\Sale;
use App\Repository\SaleRepository;
use App\Repository\StockRepository;
use App\Service\SaleService;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @Rest\Route("/api/sale")
 */
class SaleController extends AbstractController
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
     * @Rest\Get("/list")
     * @OA\Get(
     *       path="/api/sale/list",
     *       summary="Get a list of Sales",
     *       description="Returns a list of Sales",
     *       operationId="getList",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *           @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Sale::class, groups={"full"}))
     *           )
     *       ),
     *       @OA\Parameter(
     *           name="order",
     *           in="query",
     *           description="The field used to order sales",
     *           required=false,
     *           @OA\Schema(type="string")
     *       )
     *  )
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
     * @Rest\Get("/show/{id}")
     *
     * @OA\Get(
     *       path="/show/{id}",
     *       summary="Get a Sale by ID",
     *       description="Returns a Sale by its ID",
     *       operationId="getSaleById",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the sale to return",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *           @OA\JsonContent(ref=@Model(type=Sale::class, groups={"sale:read"}))
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="Sale not found",
     *           @OA\JsonContent(
     *               @OA\Property(property="error", type="string", example="Sale not found.")
     *           )
     *       )
     *  )
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
     * @Rest\Post("/create")
     * @OA\Post(
     *       path="/create",
     *       summary="Create a Sale",
     *       description="Create a new Sale",
     *       operationId="createSale",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(ref="#/components/schemas/Sale")
     *       ),
     *       @OA\Response(
     *           response=201,
     *           description="Successful operation",
     *           @OA\JsonContent(ref=@Model(type=Sale::class, groups={"sale:read"}))
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad request",
     *           @OA\JsonContent(
     *               @OA\Property(property="error", type="string", example="Error message")
     *           )
     *       )
     *  )
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
     * @Rest\Put("/update/{id}")
     * @OA\Put(
     *       path="/update/{id}",
     *       summary="Update a Sale",
     *       description="Update an existing Sale",
     *       operationId="updateSale",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the sale to update",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *       @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(ref="#/components/schemas/Sale")
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *           @OA\JsonContent(ref=@Model(type=Sale::class, groups={"sale:read"}))
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad request",
     *           @OA\JsonContent(
     *               @OA\Property(property="error", type="string", example="Error message")
     *           )
     *       )
     *  )
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
     * @Rest\Delete("/delete/{id}")
     * @OA\Delete(
     *       path="/delete/{id}",
     *       summary="Delete a Sale",
     *       description="Delete a Sale by its ID",
     *       operationId="deleteSale",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the sale to delete",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *       @OA\Response(
     *           response=204,
     *           description="No content"
     *       )
     *  )
     */
    public function delete(Sale $sale): JsonResponse
    {
        $this->entityManager->remove($sale);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

//    /**
//     * @Rest\Post("/approve/{id}")
//     * @ParamConverter("sale", class="App\Entity\Sale")
//     * @throws Exception
//     */
    /**
     * @Rest\Post("/approve/{id}")
     * @OA\Post(
     *       path="/api/sale/approve/{id}",
     *       summary="Approve a Sale",
     *       description="Approves a sale by ID",
     *       operationId="approveSale",
     *       tags={"rewards"},
     *       security={{"Bearer": {}}},
     *       @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the Sale to approve",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *           @OA\JsonContent(
     *               type="object",
     *               properties={
     *                   "message": {"type": "string"}
     *               }
     *           )
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="Sale not found",
     *           @OA\JsonContent(
     *               type="object",
     *               properties={
     *                   "error": {"type": "string"}
     *               }
     *           )
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad request",
     *           @OA\JsonContent(
     *               type="object",
     *               properties={
     *                   "error": {"type": "string"}
     *               }
     *           )
     *       )
     * )
     */
    public function approve(Sale $sale): JsonResponse
    {
        if (!$sale) {
            return new JsonResponse(['error' => 'Sale not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $result = $this->saleService->approveSale($sale);

        if (isset($result['error'])) {
            return new JsonResponse(['error' => $result['error']], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => $result['message']], JsonResponse::HTTP_OK);
    }
}