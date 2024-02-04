<?php

// src/MessageHandler/PurchaseApprovedMessageHandler.php

namespace App\MessageHandler;

use App\Entity\Stock;
use App\Message\PurchaseApprovedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PurchaseApprovedMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(PurchaseApprovedMessage $message)
    {
        $purchase = $message->getPurchase();
        $quantity = $purchase->getQuantity();
        $product = $purchase->getProduct();
        $location = $purchase->getLocation();

        // Update Stock entity
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stock = $stockRepository->findOneBy(['product' => $product, 'location' => $location]);

        if ($stock) {
            $stock->setQuantity($stock->getQuantity() + $quantity);
            $this->entityManager->flush();
        }
    }
}