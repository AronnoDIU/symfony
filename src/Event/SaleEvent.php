<?php

// src/Event/SaleEvent.php
namespace App\Event;

use App\Entity\Sale;
use Symfony\Contracts\EventDispatcher\Event;

class SaleEvent extends Event
{
    public const NAME = 'sale.updated';

    private Sale $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function getSale(): Sale
    {
        return $this->sale;
    }
}
