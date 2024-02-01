<?php

// tests/Entity/SaleTest.php

namespace App\Tests\Entity;

use App\Entity\Sale;
use App\Entity\Product;
use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SaleTest extends KernelTestCase
{
    public function testGettersAndSetters()
    {
        $sale = new Sale();
        $product = new Product();
        $location = new Location();

        // Set values
        $sale->setProduct($product);
        $sale->setLocation($location);
        $sale->setQuantity(10);
        $sale->setStatus('Approve');

        // Assert values
        $this->assertSame($product, $sale->getProduct());
        $this->assertSame($location, $sale->getLocation());
        $this->assertSame(10, $sale->getQuantity());
        $this->assertSame('Approve', $sale->getStatus());
    }
}