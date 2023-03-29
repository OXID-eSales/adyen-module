<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCancels;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;

class PaymentCancelTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCancel::setCancelResult
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCancel::getCancelResult
     */
    public function testGetCancelResult()
    {
        $cancelResult = ['success' => true];
        $paymentCancel = $this->getMockBuilder(PaymentCancel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['doAdyenCancel']) // just one method we don't need for this test
            ->getMock();
        $paymentCancel->setCancelResult($cancelResult);

        $this->assertEquals(
            $cancelResult,
            $paymentCancel->getCancelResult()
        );
    }
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentCancel::doAdyenCancel
     */
    public function testDoAdyenCapture()
    {
        $cancelResult = ['success' => true];
        $merchantAccount = 'merchantAccount';
        $pspReference = 'pspReference';
        $reference = 'reference';
        $paymentCancel = $this->createPaymentCancelMock(
            $merchantAccount,
            $cancelResult,
            $reference,
            $pspReference
        );

        $this->assertTrue(
            $paymentCancel->doAdyenCancel(
                $pspReference,
                $reference
            )
        );
    }

    private function createPaymentCancelMock(
        string $merchantAccount,
        array $cancelResult,
        string $reference,
        string $pspReference
    ): PaymentCancel {
        $cancels = $this->createAdyenApiCancelsMock($reference, $pspReference, $merchantAccount);
        $mock = $this->getMockBuilder(PaymentCancel::class)
            ->setConstructorArgs(
                [
                    $this->createModuleSettingsMock($merchantAccount),
                    $this->createAdyenAPIResponseCancelsMock($cancels, $cancelResult),
                    $this->createOxNewServiceMock($cancels)
                ]
            )
            ->onlyMethods(['setCancelResult', 'setPaymentExecutionError'])
            ->getMock();
        $mock->expects($this->once())
            ->method('setCancelResult')
            ->with($cancelResult);
        $mock->expects($this->never())
            ->method('setPaymentExecutionError');

        return $mock;
    }

    private function createOxNewServiceMock(AdyenAPICancels $adyenAPICancels): OxNewService
    {
        $mock = $this->getMockBuilder(OxNewService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['oxNew'])
            ->getMock();
        $mock->expects($this->once())
            ->method('oxNew')
            ->with(AdyenAPICancels::class)
            ->willReturn($adyenAPICancels);

        return $mock;
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

    private function createAdyenApiCancelsMock(
        string $reference,
        string $pspReference,
        string $merchantAccount
    ): AdyenAPICancels {
        $mock = $this->getMockBuilder(AdyenAPICancels::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setReference', 'setPspReference', 'setMerchantAccount'])
            ->getMock();
        $mock->expects($this->once())
            ->method('setReference')
            ->with($reference);
        $mock->expects($this->once())
            ->method('setPspReference')
            ->with($pspReference);
        $mock->expects($this->once())
            ->method('setMerchantAccount')
            ->with($merchantAccount);

        return $mock;
    }

    private function createAdyenAPIResponseCancelsMock(
        AdyenAPICancels $cancels,
        array $cancelResult
    ): AdyenAPIResponseCancels {
        $mock = $this->getMockBuilder(AdyenAPIResponseCancels::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setCancel'])
            ->getMock();
        $mock->expects($this->once())
            ->method('setCancel')
            ->with($cancels)
            ->willReturn($cancelResult);

        return $mock;
    }
}
