<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService;

class OrderTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Order::getAdyenPSPReference
     */
    public function testSetGetPSPReference(): void
    {
        $model = $this->createPartialMock(Order::class, []);

        $model->setAdyenPSPReference('testPSPReference');
        $this->assertSame('testPSPReference', $model->getAdyenPSPReference());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Order::getAdyenOrderReference
     */
    public function testSetGetAdyenOrderReference(): void
    {
        $model = $this->createPartialMock(Order::class, []);

        $model->setAdyenOrderReference('testORDERReference');
        $this->assertSame('testORDERReference', $model->getAdyenOrderReference());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Order::isAdyenCapturePossible
     */
    public function testAdyenCapturePossible(): void
    {
        $orderId = '456';

        $capturePossibleServiceMock = $this->createPartialMock(
            OrderIsAdyenCapturePossibleService::class,
            ['isAdyenCapturePossible']
        );
        $capturePossibleServiceMock->expects($this->once())
            ->method('isAdyenCapturePossible')
            ->with($orderId)
            ->willReturn(true);

        $orderMock = $this->createPartialMock(
            Order::class,
            ['getTotalOrderSum', 'getCapturedAmount', 'isAdyenOrder', 'getServiceFromContainer', 'getId']
        );

        $orderMock->method('getCapturedAmount')->willReturn(0.0);
        $orderMock->method('getTotalOrderSum')->willReturn(120.0);
        $orderMock->method('isAdyenOrder')->willReturn(true);
        $orderMock->method('getServiceFromContainer')->willReturn($capturePossibleServiceMock);
        $orderMock->method('getId')->willReturn($orderId);


        // test getPossibleCaptureAmount, isAdyenCapturePossible
        $this->assertTrue($orderMock->isAdyenCapturePossible());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Order::isAdyenCapturePossible
     */
    public function testAdyenCancelPossible(): void
    {
        $mock = $this->createPartialMock(Order::class, ['getCapturedAmount']);

        $mock->method('getCapturedAmount')->willReturn(0.0);
        $this->assertTrue($mock->isAdyenCancelPossible());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\Order::isAdyenRefundPossible
     */
    public function testAdyenRefundPossible(): void
    {
        $mock = $this->createPartialMock(Order::class, ['getCapturedAmount', 'getRefundedAmount', 'isAdyenOrder']);

        $mock->method('getCapturedAmount')->willReturn(200.0);
        $mock->method('getRefundedAmount')->willReturn(120.0);
        $mock->method('isAdyenOrder')->willReturn(true);
        // test getPossibleRefundAmount, isAdyenRefundPossible
        $this->assertTrue($mock->isAdyenRefundPossible());
    }

    /**
     * @covers OxidSolutionCatalysts\Adyen\Model\Order::finalizeOrder
     */
    public function testFinalizeOrderIsNoAdyenOrder()
    {
        $builder = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isAdyenOrder',
                'setAdyenOrderStatus',
                'getAdyenStringData',
                'getAdyenPSPReference',
                'getServiceFromContainer'
            ]);
        $orderMock = $builder->getMock();

        // Force the branch: we want the module service check to pass.
        $orderMock->method('getAdyenStringData')
            ->with('oxpaymenttype')
            ->willReturn('adyenPaymentType');

        $orderMock->method('getAdyenPSPReference')
            ->willReturn('somePSPReference');

        // Create a stub for ModuleService so that isAdyenPayment() returns true.
        $moduleServiceStub = $this->createMock(ModuleService::class);
        $moduleServiceStub->method('isAdyenPayment')
            ->willReturn(true);
        $orderMock->method('getServiceFromContainer')
            ->with(ModuleService::class)
            ->willReturn($moduleServiceStub);

        // Expect isAdyenOrder() to be called exactly once, and return false.
        $orderMock->expects($this->once())
            ->method('isAdyenOrder')
            ->willReturn(false);

        // And expect that setAdyenOrderStatus is never called.
        $orderMock->expects($this->never())
            ->method('setAdyenOrderStatus');

        // Set the required dependency so that no TypeError is thrown.
        $ref = new \ReflectionProperty(Order::class, 'queryBuilderFactory');
        $ref->setAccessible(true);
        $ref->setValue($orderMock, $this->createMock(QueryBuilderFactoryInterface::class));

        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $this->assertEquals(
            Order::ORDER_STATE_INVALIDPAYMENT,
            $orderMock->finalizeOrder($basket, $user)
        );
    }
}
