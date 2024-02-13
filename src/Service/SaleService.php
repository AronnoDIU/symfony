<?php

// src/Service/SaleService.php

namespace App\Service;

use App\Entity\Sale;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SaleService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private EmailService $emailService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator,
        EmailService           $emailService
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->emailService = $emailService;
    }

    /**
     * @throws Exception
     */
    public function createSale(Sale $sale): array
    {
        // Validate the sale
        $errors = $this->validator->validate($sale);

        if (count($errors) > 0) {
            return ['error' => (string) $errors];
        }

        try {
            // Persist the Sale entity
            $this->entityManager->persist($sale);

            // Persist each associated product individually
            foreach ($sale->getProducts() as $product) {
                $this->entityManager->persist($product);
            }

            // Flush changes to the database
            $this->entityManager->flush();

            // Optionally, you may return the persisted Sale entity along with a success message
            return ['sale' => $sale, 'message' => 'Sale created successfully'];
        } catch (\Exception $e) {
            // Handle any exceptions, log errors, and return an error message
            return ['error' => 'Failed to create sale: ' . $e->getMessage()];
        }
    }

//    public function createSale(Sale $sale): array
//    {
//        // Validate the sale
//        $errors = $this->validator->validate($sale);
//
//        if (count($errors) > 0) {
//            return ['error' => (string)$errors];
//        }
//
//        try {
//            // Persist the Sale entity
//            $this->entityManager->persist($sale);
//            $this->entityManager->flush();
//
//            // Persist associated entities explicitly
//            $this->entityManager->persist($sale->getProducts());
//
//            // Optionally, you may return the persisted Sale entity along with a success message
//            return ['sale' => $sale, 'message' => 'Sale created successfully'];
//        } catch (\Exception $e) {
//            // Handle any exceptions, log errors, and return an error message
//            return ['error' => 'Failed to create sale: ' . $e->getMessage()];
//        }
//    }

//    public function createSale(Sale $sale): array
//    {
//        // Validate the sale
//        $errors = $this->validator->validate($sale);
//
//        if (count($errors) > 0) {
//            return ['error' => (string)$errors];
////            return new JsonResponse(['error' => (string)$errors], JsonResponse::HTTP_BAD_REQUEST);
//        }
//
//        // Persist associated entities explicitly
////        $this->entityManager->persist($sale->getProducts());
////        $this->entityManager->persist($sale->getLocation());
//        $this->entityManager->persist($sale->getStock());
//        $this->entityManager->persist($sale->getLocation());
//        $this->entityManager->persist($sale->getCustomer());
//        $this->entityManager->persist($sale->getProducts());
//
//        // Persist the sale
//        $this->entityManager->persist($sale);
//        $this->entityManager->flush();
//
//        // Update stock quantity after the sale has been persisted
////        $sale->getStock()->addSale($sale);
////        $this->entityManager->flush();
//
////        // Check if the sale price is greater than 1000
////        if ($sale->getPrice() > 1000.00) {
////            // Email the customer
////            $this->emailService->sendSaleNotificationEmail($sale);
////        }
//
//        return ['sale' => $sale];
//    }

    /**
     * @throws Exception
     */
    public function approveSale(Sale $sale): array
    {
        // Check if the sale is already approved
        if ($sale->getStatus() === 'Approve') {
            return ['error' => 'Sale is already approved.'];
        }

        // Set the status to 'Approve'
        $sale->setStatus('Approve');
        $this->entityManager->flush();

        // Update stock quantity after the sale status has been changed to 'Approve'
        $sale->getStock()->addSale($sale);
        $this->entityManager->flush();

        // Check if the sale price is greater than 1000
        if ($sale->getPrice() > 1000.00) {
            // Email the customer
            $this->emailService->sendSaleNotificationEmail($sale);
        }

        return ['message' => 'Sale approved and added to Stock.'];
    }
}