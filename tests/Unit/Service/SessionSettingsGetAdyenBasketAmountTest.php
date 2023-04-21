<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Price;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsGetAdyenBasketAmountTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getAdyenBasketAmount
     */
    public function testGetAdyenBasketAmount()
    {
        $price = oxNew(Price::class);
        $price->setPrice(12.23);
        $basket = $this->getSession()->getBasket();
        $basket->setPrice($price);

        $this->assertEquals(
            1223.0,
            $this->createSessionSettings()->getAdyenBasketAmount()
        );
    }
}
