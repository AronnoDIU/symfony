<?php

//// src/Service/StockUpdaterService.php
//
//namespace App\Service;
//
//use App\Entity\Purchase;
//use App\Entity\Stock;
//use Doctrine\ORM\EntityManagerInterface;
//
//class StockUpdaterService
//{
//    private EntityManagerInterface $entityManager;
//
//    public function __construct(EntityManagerInterface $entityManager)
//    {
//        $this->entityManager = $entityManager;
//    }
//
//    public function updateStockFromPurchase(Purchase $purchase): void
//    {
//        $quantity = $purchase->getQuantity();
//        $product = $purchase->getProduct();
//        $location = $purchase->getLocation();
//
//        // Find or create Stock entity
//        $stockRepository = $this->entityManager->getRepository(Stock::class);
//        $stock = $stockRepository->findOneBy(['product' => $product, 'location' => $location]);
//
//        if (!$stock) {
//            $stock = (new Stock())
//                ->setProduct($product)
//                ->setLocation($location)
//                ->setQuantity(0);
//
//            $this->entityManager->persist($stock);
//        } else {
//            $stock->setQuantity($stock->getQuantity() + $quantity);
//        }
//        $this->entityManager->flush();
//    }
//}