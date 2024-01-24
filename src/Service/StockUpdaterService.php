<?php

// src/Service/StockUpdaterService.php

namespace App\Service;

use App\Entity\Purchase;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

class StockUpdaterService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateStockFromPurchase(Purchase $purchase): void
    {
        $quantity = $purchase->getQuantity();
        $product = $purchase->getProduct();

        // Find or create Stock entity
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stock = $stockRepository->findOneBy(['product' => $product]);

        if (!$stock) {
            $stock = new Stock();
            $stock->setProduct($product);
        }

        // Update stock quantity based on purchase status
        if ($purchase->getStatus() === 'approved') {
            $stock->increaseQuantity($quantity);
        }

        // Persist and flush changes
        $this->entityManager->persist($stock);
        $this->entityManager->flush();
    }
}