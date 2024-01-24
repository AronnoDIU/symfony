<?php

// src/Event/PurchaseEvent.php

namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseEvent extends Event
{
    public const NAME = 'purchase.updated';

    private Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
}
