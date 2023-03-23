<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service\Controller;

use OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use PHPUnit\Framework\TestCase;

class PaymentJSControllerServiceCreateOrderNumberTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\Controller\PaymentJSControllerService::createOrderReference
     * @dataProvider getCreateOrderTestData
     */
    public function testCreateOrderReference(
        string $orderReference,
        float $basketAmountValue,
        string $createdOrderReference,
        int $setAmountValueInvokeCount,
        int $createOrderReferenceInvokeCount,
        string $expectedOrderReference
    ) {
        $service = new PaymentJSControllerService(
            $this->createPaymentCancelMock(),
            $this->createSessionSettingsMock(
                $setAmountValueInvokeCount,
                $createOrderReferenceInvokeCount,
                $basketAmountValue,
                $createdOrderReference
            )
        );

        $this->assertEquals(
            $expectedOrderReference,
            $service->createOrderReference($orderReference, $basketAmountValue)
        );
    }

    public function getCreateOrderTestData(): array
    {
        return [
            [
                'orderReference',
                0.0,
                '',
                0,
                0,
                'orderReference'
            ],
            [
                '',
                1.0,
                'createdOrderReference',
                1,
                1,
                'createdOrderReference'
            ],
        ];
    }

    private function createPaymentCancelMock(): PaymentCancel
    {
        return $this->getMockBuilder(PaymentCancel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createSessionSettingsMock(
        int $setAmountValueInvokeCount,
        int $createOrderReferenceInvokeCount,
        float $basketAmountValue,
        string $createdOrderReference
    ): SessionSettings {
        $mock = $this->getMockBuilder(SessionSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setAmountValue', 'createOrderReference'])
            ->getMock();

        $mock->expects($this->exactly($setAmountValueInvokeCount))
            ->method('setAmountValue')
            ->with($basketAmountValue);

        $mock->expects($this->exactly($createOrderReferenceInvokeCount))
            ->method('createOrderReference')
            ->willReturn($createdOrderReference);

        return $mock;
    }
}
