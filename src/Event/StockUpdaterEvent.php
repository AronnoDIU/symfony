<?php

// src/Event/StockUpdaterEvent.php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class StockUpdaterEvent extends Event
{
    protected array $stocks = [];

    public function __construct($stocks)
    {
        $this->stocks = $stocks;
    }

    public function getStocks(): array
    {
        return $this->stocks;
    }
}