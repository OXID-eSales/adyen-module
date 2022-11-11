<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;

class PaymentControllerTest extends UnitTestCase
{
    public function testGetPaymentList()
    {
        $paymentController = oxNew(PaymentController::class);
        $paymentList = $paymentController->getPaymentList();

        $this->assertIsArray($paymentList);

        $this->assertArrayHasKey(Module::PAYMENT_CREDITCARD_ID, $paymentList);
        $this->assertEmpty($paymentList[Module::PAYMENT_CREDITCARD_ID]['currencies']);
        $this->assertEmpty($paymentList[Module::PAYMENT_CREDITCARD_ID]['countries']);

        $this->assertArrayHasKey(Module::PAYMENT_PAYPAL_ID, $paymentList);
        $this->assertEmpty($paymentList[Module::PAYMENT_PAYPAL_ID]['currencies']);
        $this->assertEmpty($paymentList[Module::PAYMENT_PAYPAL_ID]['countries']);
    }
}
