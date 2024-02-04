<?php

// src/Message/PurchaseApprovedMessage.php

namespace App\Message;

use App\Entity\Purchase;

class PurchaseApprovedMessage
{
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