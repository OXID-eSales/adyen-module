<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\PaymentGateway;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
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
        $paymentId = Module::PAYMENT_CREDITCARD_ID;
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock($paymentId);
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            1,
            1
        );
        $moduleServiceMock = $this->createModuleServiceMock(
            $paymentId,
            1,
            true,
            false
        );

        $paymentGatewayMock = $this->createPaymentGatewayMock(
            $sessionSettingsMock,
            $moduleServiceMock,
            $paymentGatewayServiceMock
        );

        /** @var PaymentGateway $paymentGatewayMock */
        $this->assertTrue($paymentGatewayMock->executePayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentSuccessCollectFromRequest()
    {
        $paymentId = Module::PAYMENT_PAYPAL_ID;
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock($paymentId);
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            0,
            1
        );
        $moduleServiceMock = $this->createModuleServiceMock($paymentId, 1);

        $paymentGatewayMock = $this->createPaymentGatewayMock(
            $sessionSettingsMock,
            $moduleServiceMock,
            $paymentGatewayServiceMock
        );

        /** @var PaymentGateway $paymentGatewayMock */
        $this->assertTrue($paymentGatewayMock->executePayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentInvalidAdyenPayment()
    {
        $paymentId = 'invalid';
        $orderMock = $this->createMock(Order::class);

        $sessionSettingsMock = $this->createSessionSettingsMock($paymentId);
        $paymentGatewayServiceMock = $this->createPaymentGatewayServiceMock(
            $this->amount,
            $orderMock,
            0,
            0
        );
        $moduleServiceMock = $this->createModuleServiceMock(
            $paymentId,
            0,
            false
        );


        $paymentGatewayMock = $this->createPaymentGatewayMock(
            $sessionSettingsMock,
            $moduleServiceMock,
            $paymentGatewayServiceMock
        );

        /** @var PaymentGateway $paymentGatewayMock */
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

    private function createPaymentGatewayMock(
        MockObject $sessionSettingsMock,
        MockObject $moduleServiceMock,
        MockObject $paymentGatewayServiceMock
    ): MockObject {
        $paymentGatewayMockBuilder = $this->getMockBuilder(PaymentGateway::class);
        $paymentGatewayMockBuilder->onlyMethods(['getServiceFromContainer']);
        $paymentGatewayMock = $paymentGatewayMockBuilder->getMock();
        $paymentGatewayMock->expects($this->exactly(3))
            ->method('getServiceFromContainer')
            ->withConsecutive(
                [SessionSettings::class],
                [ModuleService::class],
                [PaymentGatewayService::class]
            )
        ->willReturnOnConsecutiveCalls(
            $sessionSettingsMock,
            $moduleServiceMock,
            $paymentGatewayServiceMock
        );

        return $paymentGatewayMock;
    }

    private function createModuleServiceMock(
        string $paymentId = Module::PAYMENT_CREDITCARD_ID,
        int $showInCtrlInvokeCount = 0,
        bool $isAdyenPayment = true,
        bool $showInPaymentCtrl = true
    ): MockObject {
        $moduleServiceMock = $this->createMock(ModuleService::class);
        $moduleServiceMock->expects($this->once())
            ->method('isAdyenPayment')
            ->with($paymentId)
            ->willReturn($isAdyenPayment);

        $moduleServiceMock->expects($this->exactly($showInCtrlInvokeCount))
            ->method('showInPaymentCtrl')
            ->with($paymentId)
            ->willReturn($showInPaymentCtrl);

        return $moduleServiceMock;
    }
}
