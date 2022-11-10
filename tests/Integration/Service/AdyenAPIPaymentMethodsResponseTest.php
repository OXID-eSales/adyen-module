<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use Adyen\Service\Checkout;
use Exception;
use Monolog\Logger;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIPaymentMethodsResponse;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

class AdyenAPIPaymentMethodsResponseTest extends UnitTestCase
{
    private $testModuleSettingValues = [
        'getAPIKey' => 'dummyKey',
        'isLoggingActive' => true,
        'isSandboxMode' => true
    ];

    protected function createTestAdyenSDKLoader(): AdyenSDKLoader
    {
        $moduleSettings = $this->createConfiguredMock(ModuleSettings::class, $this->testModuleSettingValues);
        $loggingHandler = $this->createPartialMock(Logger::class, ['getName']);
        $loggingHandler->method('getName')->willReturn('Adyen AdyenAPISessionResponse Logger');

        return new AdyenSDKLoader($moduleSettings, $loggingHandler);
    }

    protected function createSession(): Session
    {
        $session = new Session();
        $session->setId('test');
        $session->setVariable(Module::ADYEN_SESSION_PAYMENTMETHODS_NAME, [['name' => 'Test CreditCard']]);

        return $session;
    }

    protected function createTestPayment(): AdyenAPIPaymentMethodsResponse
    {
        $adyenSDKLoader = $this->createTestAdyenSDKLoader();
        $session = $this->createSession();

        return new AdyenAPIPaymentMethodsResponse($adyenSDKLoader, $session);
    }

    /**
     * @throws \Exception
     */
    public function testGetAdyenPaymentMethods()
    {
        $payment = $this->createTestPayment();
        $this->assertEquals([['name' => 'Test CreditCard']], $payment->getAdyenPaymentMethods());
    }

    /**
     * @throws \Exception
     */
    public function testExceptionGetAdyenPaymentMethods()
    {
        $adyenSDKLoader = $this->createTestAdyenSDKLoader();
        $session = new Session();

        $payment = new AdyenAPIPaymentMethodsResponse($adyenSDKLoader, $session);
        $this->expectExceptionMessage('Load the paymentMethods before getting the paymentMethods');
        $payment->getAdyenPaymentMethods();
    }

    /**
     * @throws \Adyen\AdyenException
     */
    public function testLoadAdyenPaymentMethods()
    {
        $adyenAPIPaymentMethods = new AdyenAPIPaymentMethods();
        $adyenAPIPaymentMethods->setCountryCode('DE');
        $adyenAPIPaymentMethods->setShopperLocale('de_DE');
        $adyenAPIPaymentMethods->setCurrencyFilterAmount('1000');
        $adyenAPIPaymentMethods->setCurrencyName('EUR');
        $adyenAPIPaymentMethods->setMerchantAccount('TestMerchant');

        $adyenSDKLoader = $this->createTestAdyenSDKLoader();
        $session = $this->createSession();

        $checkoutMock = $this->getMockBuilder(Checkout::class)
            ->disableOriginalConstructor()->getMock();
        $checkoutMock->method('paymentMethods')->willReturn([
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            'paymentMethods' => [
                ['name' => 'Test CreditCard'],
                ['name' => 'Test PayPal'],
            ]
        ]);

        $paymentMock = $this->getMockBuilder(AdyenAPIPaymentMethodsResponse::class)
            ->onlyMethods(['createCheckout'])
            ->setConstructorArgs([$adyenSDKLoader, $session])->getMock();
        $paymentMock->method('createCheckout')
            ->willReturn($checkoutMock);

        $result = $paymentMock->loadAdyenPaymentMethods($adyenAPIPaymentMethods);

        $this->assertTrue($result);
    }

    /**
     * @throws \Adyen\AdyenException
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function testExceptionLoadAdyenPaymentMethods()
    {
        $adyenAPIPaymentMethods = new AdyenAPIPaymentMethods();
        $adyenAPIPaymentMethods->setCountryCode('DE');
        $adyenAPIPaymentMethods->setShopperLocale('de_DE');
        $adyenAPIPaymentMethods->setCurrencyFilterAmount('1000');
        $adyenAPIPaymentMethods->setCurrencyName('EUR');
        $adyenAPIPaymentMethods->setMerchantAccount('TestMerchant');

        $adyenSDKLoaderMock = $this->createTestAdyenSDKLoader();
        $sessionMock = $this->createSession();

        $checkoutMock = $this->getMockBuilder(Checkout::class)
            ->disableOriginalConstructor()->getMock();
        $checkoutMock->method('paymentMethods')->willReturn([
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            //'paymentMethods' => [
            //    ['name' => 'Test CreditCard'],
            //    ['name' => 'Test PayPal'],
            //]
        ]);

        $paymentMock = $this->getMockBuilder(AdyenAPIPaymentMethodsResponse::class)
            ->setConstructorArgs([$adyenSDKLoaderMock, $sessionMock])
            ->onlyMethods(['createCheckout'])->getMock();
        $paymentMock->method('createCheckout')
            ->willReturn($checkoutMock);

        $paymentMock->loadAdyenPaymentMethods($adyenAPIPaymentMethods);
        $this->assertLoggedException(Exception::class, 'paymentMethodsData not found in Adyen-Response');
    }

    /**
     * @throws \Adyen\AdyenException
     */
    public function testSaveAdyenPaymentMethods()
    {
        $arrayResultsAPI = [
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            'paymentMethods' => [
                ['name' => 'Test CreditCard'],
                ['name' => 'Test PayPal'],
            ]
        ];

        $payment = $this->createTestPayment();
        $result = $payment->saveAdyenPaymentMethods($arrayResultsAPI);

        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteAdyenPaymentMethods()
    {
        $payment = $this->createTestPayment();
        $payment->deleteAdyenPaymentMethods();

        $this->expectExceptionMessage('Load the paymentMethods before getting the paymentMethods');
        $payment->getAdyenPaymentMethods();
    }
}
