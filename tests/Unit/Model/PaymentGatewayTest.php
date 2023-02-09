<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\PaymentGateway;
use OxidSolutionCatalysts\Adyen\Service\PaymentGateway as PaymentGatewayService;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentGatewayTest extends UnitTestCase
{
    use ServiceContainer;

    private float $amount;

    public function setUp(): void
    {
        $this->amount = 1.0;

        parent::setUp();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentSuccess()
    {
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock();
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            0,
            1
        );

        $paymentGatewayMock = $this->createPaymentGateway($sessionSettingsMock, $paymentGatewayServiceMock);

        $this->assertTrue($paymentGatewayMock->executePayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentSuccessCollectFromRequest()
    {
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock(Module::PAYMENT_PAYPAL_ID);
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            1,
            1
        );

        $paymentGatewayMock = $this->createPaymentGateway($sessionSettingsMock, $paymentGatewayServiceMock);

        $this->assertTrue($paymentGatewayMock->executePayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentInvalidAdyenPayment()
    {
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock('invalid');
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            0,
            0
        );

        $paymentGatewayMock = $this->createPaymentGateway($sessionSettingsMock, $paymentGatewayServiceMock);

        $this->assertTrue($paymentGatewayMock->executePayment($this->amount, $orderMock));
    }

    private function createSessionSettingsMock(string $paymentId = Module::PAYMENT_CREDITCARD_ID): MockObject
    {
        $sessionSettingsMock = $this->createMock(SessionSettings::class);
        $sessionSettingsMock->expects($this->once())
            ->method('getPaymentId')
            ->willReturn($paymentId);

        return $sessionSettingsMock;
    }

    private function createPaymentGatewayServiceMock(
        float $amount,
        MockObject $orderMock,
        int $doCollectInvokeCount = 1,
        int $doFinishPaymentInvokeCount = 1
    ): MockObject {
        $sessionSettingsMock = $this->createMock(PaymentGatewayService::class);
        $sessionSettingsMock->expects($this->exactly($doCollectInvokeCount))
            ->method('doCollectAdyenRequestData');

        $sessionSettingsMock->expects($this->exactly($doFinishPaymentInvokeCount))
            ->method('doFinishAdyenPayment')
            ->with($amount, $orderMock);

        return $sessionSettingsMock;
    }

    private function createPaymentGateway(
        MockObject $sessionSettingsMock,
        MockObject $paymentGatewayServiceMock
    ): PaymentGateway {
        return oxNew(PaymentGateway::class, $sessionSettingsMock, $paymentGatewayServiceMock);
    }
}
