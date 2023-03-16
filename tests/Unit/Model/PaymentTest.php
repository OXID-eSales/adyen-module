<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module as CoreModule;
use OxidSolutionCatalysts\Adyen\Model\Payment;

class PaymentTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Payment::getTemplatePayButtonContainerId
     */
    public function testGetTemplateIdGeneral()
    {
        $paymnet = oxNew(Payment::class);
        $paymnet->setId(CoreModule::PAYMENT_PAYPAL_ID);

        $this->assertEquals(CoreModule::PAYMENT_PAYPAL_ID . '-container', $paymnet->getTemplatePayButtonContainerId());
    }
}
