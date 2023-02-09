<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Integration\Model;

use OxidEsales\EshopCommunity\Core\Session;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\PaymentGateway;
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
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentSuccess()
    {
        $sessionSettings = $this->getServiceFromContainer(SessionSettings::class);

        $this->initializeSessionValues(
            $this->pspReference,
            $this->orderReference,
            $this->resultCode,
            $this->amountCurrency,
            $this->amountValue,
            Module::PAYMENT_CREDITCARD_ID
        );

        $order = $this->createOrderMock(
            $this->orderId,
            $this->orderReference,
            $this->pspReference,
            $this->amount,
            $this->amountCurrency,
            $this->resultCode
        );

        $paymentGateway = oxNew(PaymentGateway::class);
        $this->assertTrue($paymentGateway->executePayment($this->amount, $order));

        $this->assertEquals('', $sessionSettings->getPspReference());
        $this->assertEquals('', $sessionSettings->getOrderReference());
        $this->assertEquals('', $sessionSettings->getAmountCurrency());
        $this->assertEquals(.0, $sessionSettings->getAmountValue());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentSuccessCollectFromRequest()
    {
        $sessionPspReference = 'sessionPspReference';
        $sessionResultCode = 'sessionResultCode';
        $orderReference = 'orderReference';
        $sessionAmountCurrency = 'EUR';
        $sessionSettings = $this->getServiceFromContainer(SessionSettings::class);

        $requestPspReference = 'requestPspReference';
        $requestResultCode = 'requestResultCode';
        $requestAmountCurrency = 'USD';

        $this->initializeSessionValues(
            $sessionPspReference,
            $orderReference,
            $sessionResultCode,
            $sessionAmountCurrency,
            $this->amountValue,
            Module::PAYMENT_PAYPAL_ID
        );

        $this->initializeRequestValues($requestPspReference, $requestResultCode, $requestAmountCurrency);

        $order = $this->createOrderMock(
            $this->orderId,
            $orderReference,
            $requestPspReference,
            $this->amount,
            $requestAmountCurrency,
            $requestResultCode,
            0
        );

        $paymentGateway = oxNew(PaymentGateway::class);
        $this->assertTrue($paymentGateway->executePayment($this->amount, $order));

        $this->assertEquals('', $sessionSettings->getPspReference());
        $this->assertEquals('', $sessionSettings->getOrderReference());
        $this->assertEquals('', $sessionSettings->getAmountCurrency());
        $this->assertEquals(.0, $sessionSettings->getAmountValue());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentInvalidAdyenPayment()
    {
        $this->getServiceFromContainer(SessionSettings::class);

        $this->initializeSessionValues(
            $this->pspReference,
            $this->orderReference,
            $this->resultCode,
            $this->amountCurrency,
            $this->amountValue,
            ''
        );

        $order = $this->createMock(Order::class);

        $order->expects($this->never())
            ->method('save')
            ->willReturn($this->orderId);

        $paymentGateway = oxNew(PaymentGateway::class);
        $this->assertTrue($paymentGateway->executePayment($this->amount, $order));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentNoSaveBecauseOfNoPspReference()
    {
        $this->pspReference = '';

        $this->assertNoSave();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentNoSaveBecauseOfNoResultCode()
    {
        $this->resultCode = '';

        $this->assertNoSave();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\PaymentGateway::executePayment
     */
    public function testExecutePaymentNoSaveBecauseOfNoOrderReference()
    {
        $this->orderReference = '';

        $this->assertNoSave();
    }

    private function assertNoSave()
    {
        $sessionSettings = $this->getServiceFromContainer(SessionSettings::class);

        $this->initializeSessionValues(
            $this->pspReference,
            $this->orderReference,
            $this->resultCode,
            $this->amountCurrency,
            $this->amountValue,
            Module::PAYMENT_CREDITCARD_ID
        );

        $order = $this->createOrderMock(
            $this->orderId,
            $this->orderReference,
            $this->pspReference,
            $this->amount,
            $this->amountCurrency,
            $this->resultCode,
            0,
            0
        );

        $paymentGateway = oxNew(PaymentGateway::class);
        $this->assertTrue($paymentGateway->executePayment($this->amount, $order));

        $this->assertEquals($this->pspReference, $sessionSettings->getPspReference());
        $this->assertEquals($this->orderReference, $sessionSettings->getOrderReference());
        $this->assertEquals($this->amountCurrency, $sessionSettings->getAmountCurrency());
        $this->assertEquals($this->amountValue, $sessionSettings->getAmountValue());
    }

    /**
     * we don't want to mock the GatewayPayment because we are testing it
     * we are retrieving the session from the registry and set the needed test data
     */
    private function initializeSessionValues(
        string $pspReference,
        string $orderReference,
        string $resultCode,
        string $amountCurrency,
        float $amountValue,
        string $paymentId
    ): Session {
        $session = Registry::getSession();
        $session->setVariable(SessionSettings::ADYEN_SESSION_ORDER_REFERENCE, $orderReference);
        $session->setVariable(SessionSettings::ADYEN_SESSION_PSPREFERENCE_NAME, $pspReference);
        $session->setVariable(SessionSettings::ADYEN_SESSION_RESULTCODE_NAME, $resultCode);
        $session->setVariable(SessionSettings::ADYEN_SESSION_AMOUNTCURRENCY_NAME, $amountCurrency);
        $session->setVariable(SessionSettings::ADYEN_SESSION_AMOUNTVALUE_NAME, $amountValue);
        $session->setVariable('paymentid', $paymentId);

        return $session;
    }

    private function initializeRequestValues(
        string $pspReference,
        string $resultCode,
        string $amountCurrency
    ): void {
        $_GET[Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME] = $pspReference;
        $_GET[Module::ADYEN_HTMLPARAM_RESULTCODE_NAME] = $resultCode;
        $_GET[Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME] = $amountCurrency;
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
}
