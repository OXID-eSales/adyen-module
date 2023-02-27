<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    use ServiceContainer;

    public function testSupportsCurrency()
    {
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;

        $this->assertTrue($paymentService->supportsCurrency('EUR', $paymentId));

        $paymentId = ModuleCore::PAYMENT_TWINT_ID;

        $this->assertFalse($paymentService->supportsCurrency('EUR', $paymentId));
        $this->assertTrue($paymentService->supportsCurrency('CHF', $paymentId));
    }
}
