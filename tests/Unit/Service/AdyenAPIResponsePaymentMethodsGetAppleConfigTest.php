<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use PHPUnit\Framework\TestCase;

class AdyenAPIResponsePaymentMethodsGetAppleConfigTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods::getApplePayConfiguration
     * @dataProvider getTestData
     */
    public function test($paymentMethods, $applePayPaymentMethod)
    {
        $service = $this->getMockBuilder(AdyenAPIResponsePaymentMethods::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAdyenPaymentMethods', 'getPaymentMethodByType'])
            ->getMock();
        $service->expects($this->once())
            ->method('getAdyenPaymentMethods')
            ->willReturn($paymentMethods);
        $service->expects($this->once())
            ->method('getPaymentMethodByType')
            ->with($paymentMethods['paymentMethods'], AdyenAPIResponsePaymentMethods::PAYMENT_TYPE_APPLE)
            ->willReturn($applePayPaymentMethod);

        $this->assertEquals(
            is_array($applePayPaymentMethod) ? $applePayPaymentMethod['configuration'] : null,
            $service->getApplePayConfiguration()
        );
    }

    public function getTestData(): array
    {
        return [
            [
                ['paymentMethods' => ['type' => 'apple']],
                ['configuration' => ['configurationKey' => 'configurationValue']],
            ],
            [
                ['paymentMethods' => ['type' => 'apple']],
                null
            ]
        ];
    }
}
