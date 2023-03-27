<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Controller\OrderController;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentBase;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Payment::supportsCurrency
     */
    public function testSupportsCurrency()
    {
        $paymentService = $this->getServiceFromContainer(Payment::class);
        $paymentId = ModuleCore::PAYMENT_CREDITCARD_ID;

        $this->assertTrue($paymentService->supportsCurrency('EUR', $paymentId));

        $paymentId = ModuleCore::PAYMENT_TWINT_ID;

        $this->assertFalse($paymentService->supportsCurrency('EUR', $paymentId));
        $this->assertTrue($paymentService->supportsCurrency('CHF', $paymentId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Payment::setPaymentResult
     * @covers \OxidSolutionCatalysts\Adyen\Service\Payment::getPaymentResult
     */
    public function testSetPaymentResult()
    {
        $paymentResult = ['result' => 'success'];
        $payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['collectPayments'])
            ->getMock();
        $payment->setPaymentResult($paymentResult);

        $this->assertEquals(
            $paymentResult,
            $payment->getPaymentResult()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Payment::collectPayments
     */
    public function testCollectPayments()
    {
        $paymentResponse = ['result' => 'success'];
        $currencyName = 'EUR';
        $paymentReturnUrl = 'url';
        $merchantAccount = 'merchantAccount';
        $paymentMethod = ['type' => 'apple'];
        $reference = 'reference';
        $origin = 'origin';
        $browserInfo = ['agent' => 'firefox'];
        $shopperEmail = 'shopperEmail';
        $shopperIp = 'shopperIp';
        $shopperReference = 'shopperReference';
        $shopperCountryCode = 'DE';
        $lineItems = ['name' => 'name'];
        $currencyAmount = '1223';
        $merchantApplicationName = Module::MODULE_NAME_EN;
        $merchantApplicationVersion = Module::MODULE_VERSION_FULL;
        $platformName = Module::MODULE_PLATFORM_NAME;
        $platformVersion = Module::MODULE_PLATFORM_VERSION;
        $platformIntegrator = Module::MODULE_PLATFORM_INTEGRATOR;
        $pspReference = 'pspReference';
        $resultCode = 'resultCode';
        $currencyDecimals = 2;
        $deliveryAddressMD5 = 'deliveryAddressMD5';
        $sessionChallengeToken = 'sessionChallengeToken';
        $amount = 12.23;
        $paymentState = [
            'paymentMethod' => $paymentMethod,
            'origin' => $origin,
            'browserInfo' => $browserInfo,
            'shopperEmail' => $shopperEmail,
            'shopperIP' => $shopperIp,
        ];

        $paymentService = $this->createPaymentMock(
            $paymentResponse,
            $currencyName,
            $paymentReturnUrl,
            $merchantAccount,
            $reference,
            $paymentMethod,
            $origin,
            $browserInfo,
            $shopperEmail,
            $shopperIp,
            $shopperReference,
            $shopperCountryCode,
            $lineItems,
            $currencyAmount,
            $merchantApplicationName,
            $merchantApplicationVersion,
            $platformName,
            $platformVersion,
            $platformIntegrator,
            $pspReference,
            $resultCode,
            $currencyDecimals,
            $deliveryAddressMD5,
            $sessionChallengeToken
        );

        $this->assertTrue(
            $paymentService->collectPayments(
                $amount,
                $reference,
                $paymentState,
                $this->createUserMock($shopperReference),
                $this->createViewConfigMock($sessionChallengeToken)
            )
        );
    }

    private function createPaymentMock(
        array $paymentsResponse,
        string $currencyName,
        string $paymentReturnUrl,
        string $merchantAccount,
        string $reference,
        array $paymentMethod,
        string $origin,
        array $browserInfo,
        string $shopperEmail,
        string $shopperIP,
        string $shopperReference,
        string $shopperCountryCode,
        array $lineItems,
        string $currencyAmount,
        string $merchantApplicationName,
        string $merchantApplicationVersion,
        string $platformName,
        string $platformVersion,
        string $platformIntegrator,
        string $pspReference,
        string $resultCode,
        int $currencyDecimals,
        string $deliveryAddressMD5,
        string $sessionChallengeToken
    ): Payment {
        $payments = $this->createAdyenAPIPaymentsMock(
            $currencyName,
            $reference,
            $paymentMethod,
            $origin,
            $browserInfo,
            $shopperEmail,
            $shopperIP,
            $shopperReference,
            $shopperCountryCode,
            $lineItems,
            $currencyAmount,
            $merchantAccount,
            $paymentReturnUrl,
            $merchantApplicationName,
            $merchantApplicationVersion,
            $platformName,
            $platformVersion,
            $platformIntegrator
        );
        $paymentMock = $this->getMockBuilder(Payment::class)
            ->setConstructorArgs(
                [
                    $this->createContextMock(
                        $currencyName,
                        $currencyDecimals,
                        $paymentReturnUrl,
                        $sessionChallengeToken,
                        $deliveryAddressMD5,
                        $pspReference,
                        $resultCode,
                        $currencyName
                    ),
                    $this->createModuleSettingsMock($merchantAccount),
                    $this->createAdyenAPIResponsePaymentsMock($payments, $paymentsResponse),
                    $this->createCountryRepoMock($shopperCountryCode),
                    $this->createAdyenAPILineItemServiceMock($lineItems),
                    $this->createSessionSettingsMock($pspReference, $resultCode, $currencyName),
                    $this->createOxNewServiceMock(
                        $payments,
                        $deliveryAddressMD5
                    )
                ]
            )
            ->onlyMethods(['setPaymentResult', 'setPaymentExecutionError'])
            ->getMock();
        $paymentMock->expects($this->exactly(1))
            ->method('setPaymentResult')
            ->with($paymentsResponse);
        $paymentMock->expects($this->exactly(0))
            ->method('setPaymentExecutionError')
            ->with(PaymentBase::PAYMENT_ERROR_GENERIC);

        return $paymentMock;
    }

    private function createContextMock(
        string $currencyName,
        int $currencyDecimals,
        string $paymentReturnUrl,
        string $sessionChallengeToken,
        string $deliveryAddressMD5,
        string $pspReference,
        string $resultCode
    ): Context {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getActiveCurrencyName', 'getActiveCurrencyDecimals', 'getPaymentReturnUrl'])
            ->getMock();
        $contextMock->expects($this->exactly(1))
            ->method('getActiveCurrencyName')
            ->willReturn($currencyName);
        $contextMock->expects($this->exactly(1))
            ->method('getActiveCurrencyDecimals')
            ->willReturn($currencyDecimals);
        $contextMock->expects($this->exactly(1))
            ->method('getPaymentReturnUrl')
            ->with(
                $sessionChallengeToken,
                $deliveryAddressMD5,
                $pspReference,
                $resultCode,
                $currencyName
            )
            ->willReturn($paymentReturnUrl);

        return $contextMock;
    }

    private function createModuleSettingsMock(string $merchantAccount): ModuleSettings
    {
        $mock = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMerchantAccount'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getMerchantAccount')
            ->willReturn($merchantAccount);

        return $mock;
    }

    private function createAdyenAPIResponsePaymentsMock(
        AdyenAPIPayments $payment,
        array $response
    ): AdyenAPIResponsePayments {
        $mock = $this->getMockBuilder(AdyenAPIResponsePayments::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPayments'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getPayments')
            ->with($payment)
            ->willReturn($response);

        return $mock;
    }

    private function createCountryRepoMock(string $countryIso): CountryRepository
    {
        $mock = $this->getMockBuilder(CountryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCountryIso'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getCountryIso')
            ->willReturn($countryIso);

        return $mock;
    }

    private function createAdyenAPILineItemServiceMock(array $lineItems): AdyenAPILineItemsService
    {
        $mock = $this->getMockBuilder(AdyenAPILineItemsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLineItems'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        return $mock;
    }

    private function createOxNewServiceMock(AdyenAPIPayments $payments, string $deliveryAddressMd5): OxNewService
    {
        $mock = $this->getMockBuilder(OxNewService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['oxNew'])
            ->getMock();
        $returnValueMap = [
            AdyenAPIPayments::class => $payments,
            OrderController::class => $this->createOrderControllerMock($deliveryAddressMd5),
        ];
        $mock->expects($this->exactly(2))
            ->method('oxNew')
            ->willReturnCallback(fn($argument) => $returnValueMap[$argument]);

        return $mock;
    }

    private function createUserMock(string $userId): User
    {
        $mock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        return $mock;
    }

    private function createOrderControllerMock(string $deliveryAddressMD5): OrderController
    {
        $mock = $this->getMockBuilder(OrderController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDeliveryAddressMD5'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getDeliveryAddressMD5')
            ->willReturn($deliveryAddressMD5);

        return $mock;
    }

    private function createViewConfigMock(string $sessionChallengeToken): ViewConfig
    {
        $mock = $this->getMockBuilder(ViewConfig::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSessionChallengeToken'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getSessionChallengeToken')
            ->willReturn($sessionChallengeToken);

        return $mock;
    }

    private function createSessionSettingsMock(
        string $pspReference,
        string $resultCode,
        string $currencyName
    ): SessionSettings {
        $mock = $this->getMockBuilder(SessionSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getPspReference',
                    'getResultCode',
                    'getAmountCurrency',
                ]
            )
            ->getMock();
        $mock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);
        $mock->expects($this->once())
            ->method('getResultCode')
            ->willReturn($resultCode);
        $mock->expects($this->once())
            ->method('getAmountCurrency')
            ->willReturn($currencyName);

        return $mock;
    }

    private function createAdyenAPIPaymentsMock(
        string $currencyName,
        string $reference,
        array $paymentMethod,
        string $origin,
        array $browserInfo,
        string $shopperEmail,
        string $shopperIP,
        string $shopperReference,
        string $shopperCountryCode,
        array $lineItems,
        string $currencyAmount,
        string $merchantAccount,
        string $returnUrl,
        string $merchantApplicationName,
        string $merchantApplicationVersion,
        string $platformName,
        string $platformVersion,
        string $platformIntegrator
    ): AdyenAPIPayments {
        $mock = $this->getMockBuilder(AdyenAPIPayments::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'setCurrencyName',
                    'setReference',
                    'setPaymentMethod',
                    'setOrigin',
                    'setBrowserInfo',
                    'setShopperEmail',
                    'setShopperIP',
                    'setShopperReference',
                    'setShopperCountryCode',
                    'setLineItems',
                    'setCurrencyAmount',
                    'setMerchantAccount',
                    'setReturnUrl',
                    'setMerchantApplicationName',
                    'setMerchantApplicationVersion',
                    'setPlatformName',
                    'setPlatformVersion',
                    'setPlatformIntegrator',
                ]
            )
            ->getMock();
        $mock->expects($this->once())
            ->method('setCurrencyName')
            ->with($currencyName);
        $mock->expects($this->once())
            ->method('setReference')
            ->with($reference);
        $mock->expects($this->once())
            ->method('setPaymentMethod')
            ->with($paymentMethod);
        $mock->expects($this->once())
            ->method('setOrigin')
            ->with($origin);
        $mock->expects($this->once())
            ->method('setBrowserInfo')
            ->with($browserInfo);
        $mock->expects($this->once())
            ->method('setShopperEmail')
            ->with($shopperEmail);
        $mock->expects($this->once())
            ->method('setShopperIP')
            ->with($shopperIP);
        $mock->expects($this->once())
            ->method('setShopperReference')
            ->with($shopperReference);
        $mock->expects($this->once())
            ->method('setShopperCountryCode')
            ->with($shopperCountryCode);
        $mock->expects($this->once())
            ->method('setLineItems')
            ->with($lineItems);
        $mock->expects($this->once())
            ->method('setCurrencyAmount')
            ->with($currencyAmount);
        $mock->expects($this->once())
            ->method('setMerchantAccount')
            ->with($merchantAccount);
        $mock->expects($this->once())
            ->method('setReturnUrl')
            ->with($returnUrl);
        $mock->expects($this->once())
            ->method('setMerchantApplicationName')
            ->with($merchantApplicationName);
        $mock->expects($this->once())
            ->method('setMerchantApplicationVersion')
            ->with($merchantApplicationVersion);
        $mock->expects($this->once())
            ->method('setPlatformName')
            ->with($platformName);
        $mock->expects($this->once())
            ->method('setPlatformVersion')
            ->with($platformVersion);
        $mock->expects($this->once())
            ->method('setPlatformIntegrator')
            ->with($platformIntegrator);

        return $mock;
    }
}
