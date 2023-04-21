<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\PaymentCapture;

class PaymentCaptureTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCapture::setCaptureResult
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCapture::getCaptureResult
     */
    public function testGetCaptureResult()
    {
        $captureResult = ['success' => true];
        $paymentCapture = $this->getMockBuilder(PaymentCapture::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['doAdyenCapture']) // just one method we don't need for this test
            ->getMock();
        $paymentCapture->setCaptureResult($captureResult);

        $this->assertEquals(
            $captureResult,
            $paymentCapture->getCaptureResult()
        );
    }
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCapture::doAdyenCapture
     */
    public function testDoAdyenCapture()
    {
        $captureResult = ['success' => true];
        $amount = 12.23;
        $pspReference = 'pspReference';
        $reference = 'reference';
        $paymentCapture = $this->createPaymentCaptureMock(
            12.23,
            2,
            '1223',
            $captureResult,
            'EUR',
            'merchantAccount',
            $reference,
            $pspReference
        );

        $this->assertTrue(
            $paymentCapture->doAdyenCapture(
                $amount,
                $pspReference,
                $reference
            )
        );
    }

    private function createPaymentCaptureMock(
        float $amountFloat,
        int $decimals,
        string $amountString,
        array $resultCapture,
        string $currencyName,
        string $merchantAccount,
        string $reference,
        string $pspReference
    ): PaymentCapture {
        $ApiCapturesMock = $this->createAdyenAPICapturesMock(
            $currencyName,
            $reference,
            $pspReference,
            $amountString,
            $merchantAccount
        );
        $paymentCapture = $this->getMockBuilder(PaymentCapture::class)
            ->setConstructorArgs(
                [
                    $this->createContextMock($currencyName, $decimals),
                    $this->createModuleSettingsMock($merchantAccount),
                    $this->createAdyenAPIResponseCapturesMock($ApiCapturesMock, $resultCapture),
                    $this->createOxNewServiceMock($ApiCapturesMock),
                ]
            )
            ->onlyMethods(['getAdyenAmount', 'setCaptureResult', 'setPaymentExecutionError'])
            ->getMock();
        $paymentCapture->expects($this->once())
            ->method('getAdyenAmount')
            ->with($amountFloat, $decimals)
            ->willReturn($amountString);
        $paymentCapture->expects($this->exactly(is_array($resultCapture) ? 1 : 0))
            ->method('setCaptureResult')
            ->with($resultCapture);
        $paymentCapture->expects($this->never())
            ->method('setPaymentExecutionError');

        return $paymentCapture;
    }

    private function createContextMock(
        string $currencyName,
        int $decimals
    ): Context {
        $mock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getActiveCurrencyName', 'getActiveCurrencyDecimals'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getActiveCurrencyName')
            ->willReturn($currencyName);
        $mock->expects($this->once())
            ->method('getActiveCurrencyDecimals')
            ->willReturn($decimals);

        return $mock;
    }

    private function createModuleSettingsMock(
        string $merchantAccount
    ): ModuleSettings {
        $mock = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMerchantAccount'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getMerchantAccount')
            ->willReturn($merchantAccount);

        return $mock;
    }

    private function createAdyenAPIResponseCapturesMock(
        AdyenAPICaptures $ApiCaptures,
        array $resultCapture
    ): AdyenAPIResponseCaptures {
        $mock = $this->getMockBuilder(AdyenAPIResponseCaptures::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setCapture'])
            ->getMock();
        $mock->expects($this->once())
            ->method('setCapture')
            ->with($ApiCaptures)
            ->willReturn($resultCapture);

        return $mock;
    }

    private function createOxNewServiceMock(AdyenAPICaptures $ApiCaptures): OxNewService
    {
        $mock = $this->getMockBuilder(OxNewService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['oxNew'])
            ->getMock();
        $mock->expects($this->once())
            ->method('oxNew')
            ->with(AdyenAPICaptures::class)
            ->willReturn($ApiCaptures);

        return $mock;
    }

    private function createAdyenAPICapturesMock(
        string $currencyName,
        string $reference,
        string $pspReference,
        string $amountString,
        string $merchantAccount
    ): AdyenAPICaptures {
        $mock = $this->getMockBuilder(AdyenAPICaptures::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'setCurrencyName',
                    'setReference',
                    'setPspReference',
                    'setCurrencyAmount',
                    'setMerchantAccount',
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
            ->method('setPspReference')
            ->with($pspReference);
        $mock->expects($this->once())
            ->method('setCurrencyAmount')
            ->with($amountString);
        $mock->expects($this->once())
            ->method('setMerchantAccount')
            ->with($merchantAccount);
        $mock->expects($this->once())
            ->method('setMerchantApplicationName')
            ->with(Module::MODULE_NAME_EN);
        $mock->expects($this->once())
            ->method('setMerchantApplicationVersion')
            ->with(Module::MODULE_VERSION_FULL);
        $mock->expects($this->once())
            ->method('setPlatformName')
            ->with(Module::MODULE_PLATFORM_NAME);
        $mock->expects($this->once())
            ->method('setPlatformVersion')
            ->with(Module::MODULE_PLATFORM_VERSION);
        $mock->expects($this->once())
            ->method('setPlatformIntegrator')
            ->with(Module::MODULE_PLATFORM_INTEGRATOR);

        return $mock;
    }
}
