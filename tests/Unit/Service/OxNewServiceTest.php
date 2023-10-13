<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Codeception\PHPUnit\TestCase;
use OxidEsales\Eshop\Core\Price;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class OxNewServiceTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OxNewService::oxNew
     */
    public function testOxNew()
    {
        $oxNewService = $this->getServiceFromContainer(OxNewService::class);
        $order = $oxNewService->oxNew(Order::class);

        $this->assertInstanceOf(Order::class, $order);

        $dPrice = 3.14;
        $price = $oxNewService->oxNew(Price::class, [$dPrice]);

        $this->assertInstanceOf(Price::class, $price);
        // prove constructor args correctly passed
        $this->assertEquals($dPrice, $price->getPrice());
    }
}
