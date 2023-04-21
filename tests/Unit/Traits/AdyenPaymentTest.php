<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidEsales\Eshop\Core\Price;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Application\Model\Basket;

class AdyenPaymentTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\AdyenPayment::getAdyenAmount
     */
    public function test()
    {
        $grossPrice = 12.23;

        $sessionSettings = new SessionSettings(
            $this->createSession($grossPrice),
            $this->getServiceFromContainer(Context::class)
        );
        $actual = $sessionSettings->getAdyenBasketAmount();

        $this->assertEquals($grossPrice * 100, $actual);
    }

    private function createSession(float $grossPrice): Session
    {
        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBasket'])
            ->getMock();

        $session->expects($this->once())
            ->method('getBasket')
            ->willReturn($this->createBasket($grossPrice));

        return $session;
    }

    private function createBasket(float $grossPrice): Basket
    {
        $basket = $this->getMockBuilder(Basket::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPrice'])
            ->getMock();

        $basket->expects($this->once())
            ->method('getPrice')
            ->willReturn($this->createPrice($grossPrice));

        return $basket;
    }

    private function createPrice(float $grossPrice): Price
    {
        $price = $this->getMockBuilder(Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBruttoPrice'])
            ->getMock();

        $price->expects($this->once())
            ->method('getBruttoPrice')
            ->willReturn($grossPrice);

        return $price;
    }
}
