<?php

// tests/Entity/SaleTest.php

namespace App\Tests\Entity;

use App\Entity\Customer;
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
        $customer = new Customer();

        // Set values
        $sale->setCustomer($customer);
        $sale->setProduct($product);
        $sale->setLocation($location);
        $sale->setQuantity(10);
        $sale->setPrice(1100.0);
        $sale->setStatus('Approve');

        // Assert values
        $this->assertSame($customer, $sale->getCustomer());
        $this->assertSame($product, $sale->getProduct());
        $this->assertSame($location, $sale->getLocation());
        $this->assertSame(10, $sale->getQuantity());
        $this->assertSame(1100.0, $sale->getPrice());
        $this->assertSame('Approve', $sale->getStatus());
    }
}