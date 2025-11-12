<?php

namespace App\Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCreation(): void
    {
        $product = new Product();
        $product->setLabel("Test");
        $product->setPriceHt("100.00");
        $product->setPriceTva("20.00");
        $product->setDescription("TEST TEST 123.");

        $this->assertSame("Test", $product->getLabel());
        $this->assertSame("100.00", $product->getPriceHt());
        $this->assertSame("20.00", $product->getPriceTva());
        $this->assertSame("TEST TEST 123.", $product->getDescription());
    }
}
