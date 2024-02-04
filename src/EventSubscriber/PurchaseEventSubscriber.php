<?php

// src/EventSubscriber/PurchaseEventSubscriber.php

namespace App\EventSubscriber;

use App\Entity\Purchase;
use App\Entity\Stock;
use App\Event\PurchaseEvent;
use App\Message\PurchaseApprovedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PurchaseEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PurchaseEvent::NAME => 'onPurchaseUpdated',
        ];
    }

    public function onPurchaseUpdated(PurchaseEvent $event)
    {
        $purchase = $event->getPurchase();
        $quantity = $purchase->getQuantity();
        $product = $purchase->getProduct();
        $location = $purchase->getLocation();

        // Find or create Stock entity
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stock = $stockRepository->findOneBy(['product' => $product, 'location' => $location]);

        if (!$stock) {
            $stock = (new Stock())
                ->setProduct($product)
                ->setLocation($location)
                ->setQuantity(0);

            $this->entityManager->persist($stock);
        } else {
            $stock->setQuantity($stock->getQuantity() + $quantity);
        }

        $this->entityManager->flush();

        // Dispatch a message indicating that a purchase has been approved
        $this->messageBus->dispatch(new PurchaseApprovedMessage($purchase));
    }
}