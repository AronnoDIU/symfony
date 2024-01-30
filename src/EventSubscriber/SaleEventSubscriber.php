<?php

// src/EventSubscriber/SaleEventSubscriber.php
namespace App\EventSubscriber;

use App\Event\SaleEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaleEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            SaleEvent::NAME => 'onSaleUpdated',
        ];
    }

    public function onSaleUpdated(SaleEvent $event)
    {
        $sale = $event->getSale();
        $quantity = $sale->getQuantity();
        $product = $sale->getProduct();
        $location = $sale->getLocation();

        // Find or create Stock entity
        $stockRepository = $this->entityManager->getRepository(\App\Entity\Stock::class);
        $stock = $stockRepository->findOneBy(['product' => $product, 'location' => $location]);

        if (!$stock) {
            $stock = (new \App\Entity\Stock())
                ->setProduct($product)
                ->setLocation($location)
                ->setQuantity(0);

            $this->entityManager->persist($stock);
        } else {
            $stock->setQuantity($stock->getQuantity() + $quantity);
        }

        $this->entityManager->flush();
    }

//    public function postPersist(LifecycleEventArgs $args)
//    {
//        $entity = $args->getObject();
//
//        // Check if the entity is an instance of Sale
//        if ($entity instanceof \App\Entity\Sale) {
//            $event = new SaleEvent($entity);
//            $args->getEntityManager()->getContainer()->get('event_dispatcher')->dispatch($event, SaleEvent::SALE_CREATED);
//        }
//    }
}