<?php

// src/Controller/Api/TransferController.php

namespace App\Controller\Api;

use App\Entity\Transfer;
use App\Repository\TransferRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @Rest\Route("/api/transfer")
 * @OA\Tag(name="Transfer")
 */
class TransferController extends AbstractFOSRestController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private TransferRepository $transferRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator,
        TransferRepository     $transferRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->transferRepository = $transferRepository;
    }

    /**
     * @Rest\Get("/list")
     * @OA\Get(
     *       path="/api/transfer/list",
     *       summary="Get a list of Transfers",
     *       description="Returns a list of Transfers",
     *       operationId="listTransfers",
     *       tags={"Transfer"},
     *       @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *           @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
     *           )
     *       )
     *  )
     */
    public function list(): JsonResponse
    {
        // Retrieve all transfers from the repository
        $transfers = $this->transferRepository->findAll();

        // Serialize the transfers for response
        $context = SerializationContext::create()->setGroups(['transfer:read']);
        $responseData = $this->serializer->serialize($transfers, 'json', $context);

        // Return the serialized transfers
        return new JsonResponse($responseData, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Rest\Post("/create")
     * @OA\Post(
     *       path="/api/transfer/create",
     *       summary="Create a Transfer",
     *       description="Create a new Transfer",
     *       operationId="createTransfer",
     *       tags={"Transfer"},
     *       @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(ref="#/components/schemas/Transfer")
     *       ),
     *       @OA\Response(
     *           response=201,
     *           description="Successful operation",
     *           @OA\JsonContent(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
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
    public function create(Request $request): JsonResponse
    {
        // Deserialize the request data into a Transfer object
        $transfer = $this->serializer->deserialize($request->getContent(), Transfer::class, 'json');

        // Validate the Transfer object
        $errors = $this->validator->validate($transfer);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Save the Transfer object to the database
        $this->entityManager->persist($transfer);
        $this->entityManager->flush();

        // Serialize the created Transfer object for response
        $context = SerializationContext::create()->setGroups(['transfer:read']);
        $responseData = $this->serializer->serialize($transfer, 'json', $context);

        // Return the serialized Transfer object with HTTP status code 201 (Created)
        return new JsonResponse($responseData, JsonResponse::HTTP_CREATED, [], true);
    }

    /**
     * @Rest\Get("/{id}")
     * @OA\Get(
     *     path="/api/transfer/{id}",
     *     summary="Get a Transfer by ID",
     *     description="Returns a Transfer by its ID",
     *     operationId="getTransferById",
     *     tags={"Transfer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transfer to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Transfer not found.")
     *         )
     *     )
     * )
     */
    public function show(Transfer $transfer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['transfer:read']);
        $data = $this->serializer->serialize($transfer, 'json', $context);
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Rest\Put("/{id}")
     * @OA\Put(
     *     path="/api/transfer/{id}",
     *     summary="Update a Transfer",
     *     description="Update a Transfer by its ID",
     *     operationId="updateTransfer",
     *     tags={"Transfer"},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of the transfer to update",
     *     required=true,
     *     @OA\Schema(type="integer")
     *    ),
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Transfer")
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *     @OA\Property(property="error", type="string", example="Error message")
     *  )
     * )
     * )
     */
    public function update(Transfer $transfer, Request $request): JsonResponse
    {
        // Implementation of update action with deserialization, validation, and serialization
        $data = json_decode($request->getContent(), true);

        // Create a DeserializationContext
        $context = DeserializationContext::create()->setGroups(['transfer:write']);

        $this->serializer->deserialize($request->getContent(), Transfer::class, 'json', $context, ['object_to_populate' => $transfer]);

        $errors = $this->validator->validate($transfer);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        // Create a SerializationContext
        $serializationContext = SerializationContext::create()->setGroups(['transfer:read']);
        $data = $this->serializer->serialize($transfer, 'json', $serializationContext);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/{id}")
     * @OA\Delete(
     *     path="/api/transfer/{id}",
     *     summary="Delete a Transfer",
     *     description="Delete a Transfer by its ID",
     *     operationId="deleteTransfer",
     *     tags={"Transfer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transfer to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Transfer not found.")
     *         )
     *     )
     * )
     */
    public function delete(Transfer $transfer): JsonResponse
    {
        $this->entityManager->remove($transfer);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("/approve/{id}")
     * @OA\Post(
     *     path="/api/transfer/approve/{id}",
     *     summary="Approve a Transfer",
     *     description="Approve a Transfer by its ID",
     *     operationId="approveTransfer",
     *     tags={"Transfer"},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of the transfer to approve",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
     * ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *     @OA\Property(property="error", type="string", example="Error message")
     * )
     * )
     * )
     */
    public function approve(Transfer $transfer): JsonResponse
    {
        // Implementation of approve action with validation and serialization
        $transfer->setStatus('Approve');
        $this->entityManager->flush();

        $context = SerializationContext::create()->setGroups(['transfer:read']);
        $data = $this->serializer->serialize($transfer, 'json', $context);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Rest\Post("/cancel/{id}")
     * @OA\Post(
     *     path="/api/transfer/cancel/{id}",
     *     summary="Cancel a Transfer",
     *     description="Cancel a Transfer by its ID",
     *     operationId="cancelTransfer",
     *     tags={"Transfer"},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of the transfer to cancel",
     *     required=true,
     *     @OA\Schema(type="integer")
     *  ),
     *     @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref=@Model(type=Transfer::class, groups={"transfer:read"}))
     * ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *     @OA\Property(property="error", type="string", example="Error message")
     * )
     * )
     * )
     */
    public function cancel(Transfer $transfer): JsonResponse
    {
        $transfer->setStatus('Cancel');
        $this->entityManager->flush();

        $context = SerializationContext::create()->setGroups(['transfer:read']);
        $data = $this->serializer->serialize($transfer, 'json', $context);

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
}