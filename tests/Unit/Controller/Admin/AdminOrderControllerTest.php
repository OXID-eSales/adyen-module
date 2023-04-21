<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Controller\Admin;

use OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController;
use OxidSolutionCatalysts\Adyen\Model\Order;
use PHPUnit\Framework\TestCase;

class AdminOrderControllerTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController::cancelAdyenOrder
     */
    public function testCancelAdyenOrder()
    {
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['cancelOrder'])
            ->getMock();
        $orderMock->expects($this->once())
            ->method('cancelOrder');

        $controllerMock = $this->getMockBuilder(AdminOrderController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEditObject'])
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getEditObject')
            ->willReturn($orderMock);

        $controllerMock->cancelAdyenOrder();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController::refundAdyenAmount
     */
    public function testRefundAdyenAmount()
    {
        $refundAmount = 12.00;
        $_GET['refund_amount'] = $refundAmount;

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['refundAdyenOrder'])
            ->getMock();
        $orderMock->expects($this->once())
            ->method('refundAdyenOrder')
            ->with($refundAmount);

        $controllerMock = $this->getMockBuilder(AdminOrderController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEditObject'])
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getEditObject')
            ->willReturn($orderMock);

        $controllerMock->refundAdyenAmount();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController::captureAdyenAmount
     */
    public function testCaptureAdyenOrder()
    {
        $captureAmount = 12.00;
        $_GET['capture_amount'] = $captureAmount;

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['captureAdyenOrder'])
            ->getMock();
        $orderMock->expects($this->once())
            ->method('captureAdyenOrder')
            ->with($captureAmount);

        $controllerMock = $this->getMockBuilder(AdminOrderController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEditObject'])
            ->getMock();
        $controllerMock->expects($this->once())
            ->method('getEditObject')
            ->willReturn($orderMock);

        $controllerMock->captureAdyenAmount();
    }
}
