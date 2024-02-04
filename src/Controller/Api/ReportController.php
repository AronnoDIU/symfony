<?php

// src/Controller/Api/ReportController.php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\Sale;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/customer-sale/{customerId}", methods={"GET"})
     */
    public function customerSaleReportById(int $customerId, SaleRepository $saleRepository): JsonResponse
    {
        // Find the customer by ID
        $customer = $this->getDoctrine()->getRepository(Customer::class)->find($customerId);

        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Fetch sales for the specified customer
        $sales = $saleRepository->findBy(['customer' => $customer]);

        // Create a response array with required details
        $reportData = [];
        $reportData['customer'] = [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ];
        $reportData['sales'] = $this->formatSalesData($sales);

        return new JsonResponse($reportData, JsonResponse::HTTP_OK);
    }

    /**
     * Format sales data for response
     */
    private function formatSalesData(array $sales): array
    {
        $formattedSales = [];

        foreach ($sales as $sale) {
            $formattedSales[] = [
                'id' => $sale->getId(),
                'product' => $sale->getProduct()->getName(),
                'amount' => $sale->getQuantity() * $sale->getPrice(),
                'quantity' => $sale->getQuantity(),
                'status' => $sale->getStatus(),
            ];
        }

        return $formattedSales;
    }
}