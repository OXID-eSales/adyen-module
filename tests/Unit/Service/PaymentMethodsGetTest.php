<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\PaymentMethods;
use OxidSolutionCatalysts\Adyen\Core\Module;
use PHPUnit\Framework\TestCase;

class PaymentMethodsGetTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentMethods::getAdyenPaymentMethods
     */
    public function testGetAdyenPaymentMethods(): void
    {
        $paymentMethods = [Module::PAYMENT_PAYPAL_ID];
        $paymentMethodsResponse = $this->createAdyenAPIResponsePaymentMethodsMock($paymentMethods);

        $paymentMethodService = $this->createPartialMock(
            PaymentMethods::class,
            ['collectAdyenPaymentMethods']
        );
        $paymentMethodService->expects($this->once())
            ->method('collectAdyenPaymentMethods')
            ->willReturn($paymentMethodsResponse);

        $actual = $paymentMethodService->getAdyenPaymentMethods();

        $this->assertEquals($paymentMethods, $actual);
    }

    private function createAdyenAPIResponsePaymentMethodsMock(array $paymentMethods): AdyenAPIResponsePaymentMethods
    {
        $mock = $this->getMockBuilder(AdyenAPIResponsePaymentMethods::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getAdyenPaymentMethods',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getAdyenPaymentMethods')
            ->willReturn($paymentMethods);

        return $mock;
    }
}
