<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service\Controller;

use OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use PHPUnit\Framework\TestCase;

class PaymentJSControllerServiceCancelIfNecessaryTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService::cancelPaymentIfNecessary
     * @covers \OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService::__construct
     * @dataProvider getCancelTestData
     */
    public function testCancelPayment(
        string $pspReference,
        string $orderReference,
        float $basketAmountValue,
        float $sessionAmountValue,
        int $doCancelInvokeCount,
        int $getAmountValueInvokeCount
    ) {
        $service = new PaymentJSControllerService(
            $this->createPaymentCancelMock(
                $doCancelInvokeCount,
                $pspReference,
                $orderReference
            ),
            $this->createSessionSettingsMock(
                $getAmountValueInvokeCount,
                $sessionAmountValue
            )
        );
        $service->cancelPaymentIfNecessary($pspReference, $basketAmountValue, $orderReference);
    }

    public function getCancelTestData(): array
    {
        return [
            [
                '',
                'orderReference',
                0.0,
                20.0,
                0,
                0
            ],
            [
                'pspReference',
                'orderReference',
                0.0,
                0.0,
                0,
                1
            ],
            [
                'pspReference',
                'orderReference',
                20.0,
                0.0,
                1,
                1
            ],
        ];
    }

    private function createPaymentCancelMock(
        int $doCancelInvokeCount,
        string $pspReference,
        string $orderReference
    ): PaymentCancel {
        $mock = $this->getMockBuilder(PaymentCancel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['doAdyenCancel'])
            ->getMock();

        $mock->expects($this->exactly($doCancelInvokeCount))
            ->method('doAdyenCancel')
            ->with($pspReference, $orderReference);

        return $mock;
    }

    private function createSessionSettingsMock(
        int $getAmountValueInvokeCount,
        float $sessionAmountValue
    ): SessionSettings {
        $mock = $this->getMockBuilder(SessionSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAmountValue', 'createOrderReference'])
            ->getMock();

        $mock->expects($this->exactly($getAmountValueInvokeCount))
            ->method('getAmountValue')
            ->willReturn($sessionAmountValue);

        return $mock;
    }
}
