<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\PaymentGateway;
use OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentGatewayOrderSavableTest extends UnitTestCase
{
    use ServiceContainer;

    private string $pspReference;
    private string $resultCode;
    private string $orderReference;
    private string $orderId;
    private float $amount;
    private string $amountCurrency;
    private float $amountValue;

    public function setUp(): void
    {
        $this->pspReference = 'pspReference';
        $this->resultCode = 'resultCode';
        $this->orderReference = 'orderReference';
        $this->amount = 1.0;
        $this->orderId = 'orderId';
        $this->amountCurrency = 'EUR';
        $this->amountValue = 2.45;

        parent::setUp();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable::prove
     */
    public function testProveSuccess()
    {
        $paymentGatewayOrderSavable = oxNew(PaymentGatewayOrderSavable::class);
        $this->assertTrue(
            $paymentGatewayOrderSavable->prove(
                $this->pspReference,
                $this->resultCode,
                $this->orderReference
            )
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable::prove
     */
    public function testProveNotSavableBecauseOfPspReference()
    {
        $paymentGatewayOrderSavable = oxNew(PaymentGatewayOrderSavable::class);
        $this->assertFalse(
            $paymentGatewayOrderSavable->prove(
                '',
                $this->resultCode,
                $this->orderReference
            )
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable::prove
     */
    public function testProveNotSavableBecauseOfResultCode()
    {
        $paymentGatewayOrderSavable = oxNew(PaymentGatewayOrderSavable::class);
        $this->assertFalse(
            $paymentGatewayOrderSavable->prove(
                $this->pspReference,
                '',
                $this->orderReference
            )
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable::prove
     */
    public function testProveNotSavableBecauseOfOrderReference()
    {
        $paymentGatewayOrderSavable = oxNew(PaymentGatewayOrderSavable::class);
        $this->assertFalse(
            $paymentGatewayOrderSavable->prove(
                $this->pspReference,
                $this->resultCode,
                ''
            )
        );
    }
}
