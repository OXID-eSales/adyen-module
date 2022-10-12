<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use Adyen\Service\Checkout;
use Monolog\Logger;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Payment;

class PaymentTest extends UnitTestCase
{
    private $moduleSettingValues = [
        'getAPIKey' => 'dummyKey',
        'isLoggingActive' => true,
        'isSandboxMode' => true
    ];

    protected function createTestAdyenSDKLoader(): AdyenSDKLoader
    {
        $moduleSettings = $this->createConfiguredMock(ModuleSettings::class, $this->moduleSettingValues);
        $loggingHandler = $this->createPartialMock(Logger::class, ['getName']);
        $loggingHandler->method('getName')->willReturn('Adyen Payment Logger');

        return new AdyenSDKLoader($moduleSettings, $loggingHandler);
    }

    protected function createSession(): Session
    {
        $session = new Session();
        $session->setId('test');
        $session->setVariable(Module::ADYEN_SESSION_ID_NAME, 'test_session_id');
        $session->setVariable(Module::ADYEN_SESSION_DATA_NAME, 'test_session_data');

        return $session;
    }

    protected function createTestPayment(): Payment
    {
        $adyenSDKLoader = $this->createTestAdyenSDKLoader();
        $session = $this->createSession();

        return new Payment($adyenSDKLoader, $session);
    }

    /**
     * @throws \Exception
     */
    public function testGetAdyenSessionId()
    {
        $payment = $this->createTestPayment();
        $this->assertEquals('test_session_id', $payment->getAdyenSessionId());
    }

    /**
     * @throws \Exception
     */
    public function testGetAdyenSessionData()
    {
        $payment = $this->createTestPayment();
        $this->assertEquals('test_session_data', $payment->getAdyenSessionData());
    }

    /**
     * @throws \Adyen\AdyenException
     */
    public function testLoadAdyenSession()
    {
        $adyenAPISession = new AdyenAPISession();
        $adyenAPISession->setCountryCode('DE');
        $adyenAPISession->setCurrencyFilterAmount('1000');
        $adyenAPISession->setCurrencyName('EUR');
        $adyenAPISession->setMerchantAccount('TestMerchant');
        $adyenAPISession->setReference('TestReference');
        $adyenAPISession->setReturnUrl('ReturnUrl');

        $adyenSDKLoader = $this->createTestAdyenSDKLoader();
        $session = $this->createSession();

        $checkoutMock = $this->getMockBuilder(Checkout::class)
            ->disableOriginalConstructor()->getMock();
        $checkoutMock->method('sessions')->willReturn([
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            'reference' => 'TestReference',
            'returnUrl' => 'ReturnUrl',
            'sessionData' => 'TestSessionData',
            'id' => 'TestSessionId'
        ]);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->setConstructorArgs([$adyenSDKLoader, $session])->getMock();
        $paymentMock->method('createCheckout')
            ->willReturn($checkoutMock);

        $result = $paymentMock->loadAdyenSession($adyenAPISession);

        $this->assertTrue($result);
    }

    /**
     * @throws \Adyen\AdyenException
     */
    public function testSaveAdyenSession()
    {
        $arrayResultsAPI = [
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            'reference' => 'TestReference',
            'returnUrl' => 'ReturnUrl',
            'sessionData' => 'TestSessionData',
            'id' => 'TestSessionId'
        ];

        $payment = $this->createTestPayment();
        $result = $payment->saveAdyenSession($arrayResultsAPI);

        $this->assertTrue($result);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteAdyenSession()
    {
        $payment = $this->createTestPayment();
        $payment->deleteAdyenSession();

        $this->expectExceptionMessage('Load the session before getting the session id');
        $payment->getAdyenSessionId();

        $this->expectExceptionMessage('Load the session before getting the session data');
        $payment->getAdyenSessionData();
    }
}
