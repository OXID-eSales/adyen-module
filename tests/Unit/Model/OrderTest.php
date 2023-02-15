<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Order;

//use OxidEsales\Eshop\Application\Model\Order;

class OrderTest extends UnitTestCase
{
    public function testSetGetPSPReference(): void
    {
        $model = $this->createPartialMock(Order::class, []);

        $model->setAdyenPSPReference('testPSPReference');
        $this->assertSame('testPSPReference', $model->getAdyenPSPReference());
    }

    public function testSetGetAdyenOrderReference(): void
    {
        $model = $this->createPartialMock(Order::class, []);

        $model->setAdyenOrderReference('testORDERReference');
        $this->assertSame('testORDERReference', $model->getAdyenOrderReference());
    }

    public function testAdyenCapturePossible(): void
    {
        $mock = $this->createPartialMock(Order::class, ['getTotalOrderSum', 'getCapturedAmount', 'isAdyenOrder']);

        $mock->method('getCapturedAmount')->willReturn(0.0);
        $mock->method('getTotalOrderSum')->willReturn(120.0);
        $mock->method('isAdyenOrder')->willReturn(true);
        // test getPossibleCaptureAmount, isAdyenCapturePossible
        $this->assertTrue($mock->isAdyenCapturePossible());
    }

    public function testAdyenCancelPossible(): void
    {
        $mock = $this->createPartialMock(Order::class, ['getCapturedAmount']);

        $mock->method('getCapturedAmount')->willReturn(0.0);
        $this->assertTrue($mock->isAdyenCancelPossible());
    }

    public function testAdyenRefundPossible(): void
    {
        $mock = $this->createPartialMock(Order::class, ['getCapturedAmount', 'getRefundedAmount', 'isAdyenOrder']);

        $mock->method('getCapturedAmount')->willReturn(200.0);
        $mock->method('getRefundedAmount')->willReturn(120.0);
        $mock->method('isAdyenOrder')->willReturn(true);
        // test getPossibleRefundAmount, isAdyenRefundPossible
        $this->assertTrue($mock->isAdyenRefundPossible());
    }
}
