<?php

// src/Service/SaleService.php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Location;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Sale\Product as SaleProduct;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
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
    public function createSale(Sale $sale, array $requestData): array
    {
        // Validate the sale
        $errors = $this->validator->validate($sale);

        if (count($errors) > 0) {
            return ['error' => (string)$errors];
        }

        try {
            // Retrieve customer and location entities
            $customerId = $requestData['customer_id'];
            $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
            $locationId = $requestData['location_id'];
            $location = $this->entityManager->getRepository(Location::class)->find($locationId);

            // Create a new Sale entity
            $sale = new Sale();
            $sale->setCustomer($customer);
            $sale->setLocation($location);
            $this->entityManager->persist($sale);

            // Iterate over products and add them to the sale
            foreach ($requestData['products'] as $productData) {
                // Retrieve product details
//                $productId = $productData['id'];

                $productId = $productData['original_id'];
                $productPrice = $productData['price'];
                $productQuantity = $productData['quantity'];

                // Retrieve product entity
                $product = $this->entityManager->getRepository(\App\Entity\Product::class)->find($productId);

                // Create a new SaleProduct entity
                $saleProduct = new SaleProduct();
                $saleProduct->setOriginal($product);
                $saleProduct->setPrice($productPrice);
                $saleProduct->setQuantity($productQuantity);

                // Add the sale product to the sale
                $sale->addProduct($saleProduct);
            }

            // Flush changes to the database
            $this->entityManager->flush();

            // Update stock quantity after the sale has been persisted
            $sale->getStock()->addSale($sale);
            $this->entityManager->flush();

            // Optionally, return the persisted Sale entity along with a success message
            return ['sale' => $sale, 'message' => 'Sale created successfully'];
        } catch (Exception $e) {
//            Throw new Exception($e->getMessage());
            return ['error' => 'Failed to create sale: ' . $e->getMessage()];
        }
    }

    /**
     * @throws Exception
     */
    public function approveSale(Sale $sale, SaleProduct $saleProduct): array
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
        if ($saleProduct->getPrice() > 1000) {
            // Email the customer
            $this->emailService->sendSaleNotificationEmail($sale);
        }

        return ['message' => 'Sale approved and added to Stock.'];
    }
}
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
//}