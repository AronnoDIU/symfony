<?php

// src/Event/StockShowEvent.php

namespace App\Event;

use App\Entity\Stock;
use Symfony\Contracts\EventDispatcher\Event;

class StockShowEvent extends Event
{
    private Stock $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

}