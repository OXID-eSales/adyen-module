<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\SessionSettings;

class SessionSettingsGetPaymentIdTest extends AbstractSessionSettingsTest
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getPaymentId
     */
    public function testGetPaymentIdPaymentIdAlreadySet()
    {
        $paymentId = 'paymentId';
        $this->getSession()->setVariable('paymentid', $paymentId);

        $this->assertEquals(
            $paymentId,
            $this->createSessionSettings()->getPaymentId()
        );
    }
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\SessionSettings::getPaymentId
     */
    public function testGetPaymentIdPaymentIdIsNull()
    {
        $this->getSession()->deleteVariable('paymentid');
        $this->getSession()->setBasket(null);

        $this->assertEquals(
            '',
            $this->createSessionSettings()->getPaymentId()
        );
    }
}
