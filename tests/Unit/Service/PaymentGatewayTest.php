<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\OrderReturnService;
use OxidSolutionCatalysts\Adyen\Service\PaymentConfigService;
use OxidSolutionCatalysts\Adyen\Service\PaymentGateway as PaymentGatewayService;
use OxidSolutionCatalysts\Adyen\Service\PaymentGatewayOrderSavable;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentGatewayTest extends UnitTestCase
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
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGateway::doFinishAdyenPayment
     */
    public function testDoFinishAdyenPaymentSuccess()
    {
        $paymentId = Module::PAYMENT_PAYPAL_ID;
        $sessionSettingsMock = $this->createSessionSettingsMock(
            1,
            $paymentId,
            $this->pspReference,
            $this->resultCode,
            $this->orderReference
        );

        $paymentGatewayOrderSavableMock = $this->createPaymentGatewayOrderSavableMock(
            true,
            $this->pspReference,
            $this->resultCode,
            $this->orderReference,
            1
        );

        $orderMock = $this->createOrderMock(
            $this->orderId,
            $this->orderReference,
            $this->pspReference,
            $this->amount,
            $this->amountCurrency,
            $this->resultCode,
            1,
            1
        );

        $paymentConfigServiceMock = $this->createPaymentConfigServiceMock(
            $paymentId
        );

        $paymentGateway = oxNew(
            PaymentGatewayService::class,
            $sessionSettingsMock,
            $paymentGatewayOrderSavableMock,
            $paymentConfigServiceMock,
            $this->getServiceFromContainer(OrderReturnService::class)
        );

        /** @var Order $orderMock */
        $this->assertTrue($paymentGateway->doFinishAdyenPayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGateway::doFinishAdyenPayment
     */
    public function testDoFinishAdyenPaymentNoSavable()
    {
        $paymentId = Module::PAYMENT_CREDITCARD_ID;
        $sessionSettingsMock = $this->createSessionSettingsMock(
            0,
            $paymentId,
            $this->pspReference,
            $this->resultCode,
            $this->orderReference
        );

        $paymentGatewayOrderSavableMock = $this->createPaymentGatewayOrderSavableMock(
            false,
            $this->pspReference,
            $this->resultCode,
            $this->orderReference,
            1
        );

        $orderMock = $this->createOrderMock(
            $this->orderId,
            $this->orderReference,
            $this->pspReference,
            $this->amount,
            $this->amountCurrency,
            $this->resultCode,
            0,
            0
        );

        $paymentConfigServiceMock = $this->createPaymentConfigServiceMock(
            $paymentId,
            0
        );

        $paymentGateway = oxNew(
            PaymentGatewayService::class,
            $sessionSettingsMock,
            $paymentGatewayOrderSavableMock,
            $paymentConfigServiceMock,
            $this->getServiceFromContainer(OrderReturnService::class)
        );

        /** @var Order $orderMock */
        $this->assertFalse($paymentGateway->doFinishAdyenPayment($this->amount, $orderMock));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentGateway::doCollectAdyenRequestData
     */
    public function testDoCollectAdyenRequestData()
    {
        $_GET[Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME] = $this->pspReference;
        $_GET[Module::ADYEN_HTMLPARAM_RESULTCODE_NAME] = $this->resultCode;
        $_GET[Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME] = $this->amountCurrency;

        $sessionSettingsMock = $this->createMock(SessionSettings::class);

        $sessionSettingsMock->expects($this->once())
            ->method('setPspReference')
            ->with($this->pspReference);

        $sessionSettingsMock->expects($this->once())
            ->method('setResultCode')
            ->with($this->resultCode);

        $sessionSettingsMock->expects($this->once())
            ->method('setAmountCurrency')
            ->with($this->amountCurrency);

        $paymentGatewayOrderSavableMock = $this->createPaymentGatewayOrderSavableMock(
            true,
            $this->pspReference,
            $this->resultCode,
            $this->orderReference,
            0
        );

        $paymentConfigServiceMock = $this->createPaymentConfigServiceMock(
            '',
            0
        );

        $paymentGateway = oxNew(
            PaymentGatewayService::class,
            $sessionSettingsMock,
            $paymentGatewayOrderSavableMock,
            $paymentConfigServiceMock,
            $this->getServiceFromContainer(OrderReturnService::class)
        );

        /** @var Order $orderMock */
        $paymentGateway->doCollectAdyenRequestData();
    }

    private function createOrderMock(
        string $orderId,
        string $orderReference,
        string $pspReference,
        float $amount,
        string $amountCurrency,
        string $resultCode,
        int $captureAdyenOrderInvokedCount = 1,
        int $saveInvokedCount = 1
    ): MockObject {
        $order = $this->createMock(Order::class);

        $order->expects($this->exactly($saveInvokedCount))
            ->method('getId')
            ->willReturn($orderId);

        $order->expects($this->exactly($saveInvokedCount))
            ->method('setAdyenOrderReference')
            ->with($orderReference);

        $order->expects($this->exactly($saveInvokedCount))
            ->method('setAdyenPSPReference')
            ->with($pspReference);

        $orderCurrency = new \stdClass();
        $orderCurrency->name = 'EUR';

        $order->expects($this->once())
            ->method('getOrderCurrency')
            ->willReturn($orderCurrency);

        $order->expects($this->exactly($saveInvokedCount))
            ->method('setAdyenHistoryEntry')
            ->with(
                $pspReference,
                $pspReference,
                $orderId,
                $amount,
                $amountCurrency,
                $resultCode,
                Module::ADYEN_ACTION_AUTHORIZE
            );

        $order->expects($this->exactly($saveInvokedCount))
            ->method('save');

        $order->expects($this->exactly($captureAdyenOrderInvokedCount))
            ->method('captureAdyenOrder');

        return $order;
    }

    private function createSessionSettingsMock(
        int $deleteInvokeCount = 0,
        string $paymentId = Module::PAYMENT_CREDITCARD_ID,
        string $pspReference = 'pspReference',
        string $resultCode = 'resultCode',
        string $orderReference = 'orderReference'
    ): MockObject {
        $sessionSettingsMock = $this->createMock(SessionSettings::class);

        $sessionSettingsMock->expects($this->exactly($deleteInvokeCount))
            ->method('deletePaymentSession');

        $sessionSettingsMock->expects($this->once())
            ->method('getPaymentId')
            ->willReturn($paymentId);

        $sessionSettingsMock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);

        $sessionSettingsMock->expects($this->once())
            ->method('getResultCode')
            ->willReturn($resultCode);

        $sessionSettingsMock->expects($this->once())
            ->method('getOrderReference')
            ->willReturn($orderReference);

        return $sessionSettingsMock;
    }

    private function createPaymentGatewayOrderSavableMock(
        bool $isSavable = true,
        string $pspReference = 'pspReference',
        string $resultCode = 'resultCode',
        string $orderReference = 'orderReference',
        int $proveInvokeCount = 1
    ): MockObject {
        $sessionSettingsMock = $this->createMock(PaymentGatewayOrderSavable::class);
        $sessionSettingsMock->expects($this->exactly($proveInvokeCount))
            ->method('prove')
            ->with($pspReference, $resultCode, $orderReference)
            ->willReturn($isSavable);

        return $sessionSettingsMock;
    }

    private function createPaymentConfigServiceMock(
        string $paymentId = Module::PAYMENT_CREDITCARD_ID,
        int $isAdyenImmediateCaptureInvokeCount = 1,
        bool $isAdyenImmediateCapture = true
    ): MockObject {
        $paymentConfigServiceMock = $this->createMock(PaymentConfigService::class);

        $paymentConfigServiceMock->expects($this->exactly($isAdyenImmediateCaptureInvokeCount))
            ->method('isAdyenImmediateCapture')
            ->with($paymentId)
            ->willReturn($isAdyenImmediateCapture);

        return $paymentConfigServiceMock;
    }
}
