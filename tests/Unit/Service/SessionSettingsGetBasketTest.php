<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsGetBasketTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getBasket
     */
    public function testGetBasket()
    {
        $paymentId = 'paymentId';
        $this->getSession()->setVariable('paymentid', $paymentId);

        $this->assertInstanceOf(
            Basket::class,
            $this->createSessionSettings()->getBasket()
        );

        $this->assertEquals(
            $paymentId,
            $this->createSessionSettings()->getBasket()->getPaymentId()
        );
    }
}
