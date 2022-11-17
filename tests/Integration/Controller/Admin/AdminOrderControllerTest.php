<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Controller\Admin;

use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController;
use OxidSolutionCatalysts\Adyen\Core\Module;

/*
 * Here we have tests for a what we call 'chain extended' shop class.
 * Current module might not be the only one extending the same class/method.
 * Always use the unified namespace name of the class instantiated with oxNew()
 * when testing.
 */
final class AdminOrderControllerTest extends UnitTestCase
{
    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestOrderData() as $dataSet) {
            [$orderId, $orderData] = $dataSet;
            $order = oxNew(Order::class);
            $order->setId($orderId);
            $order->assign($orderData);
            $order->save();
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestOrderData() as $dataSet) {
            [$orderId, ] = $dataSet;
            $order = oxNew(Order::class);
            $order->load($orderId);
            $order->delete();
        }
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testRender($orderId, $orderData): void
    {
        $controller = $this->createPartialMock(AdminOrderController::class, ['getEditObjectId']);
        $controller->expects($this->any())->method('getEditObjectId')->willReturn($orderId);

        $this->assertSame('osc_adyen_order.tpl', $controller->render());

        $viewData = $controller->getViewData();
        $this->assertSame($orderId, $viewData['oxid']);
        $this->assertInstanceOf(Order::class, $viewData['edit']);
        $this->assertSame($orderId, $viewData['edit']->getId());
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testIsAdyenOrder($orderId, $orderData): void
    {
        $controller = $this->createPartialMock(AdminOrderController::class, ['getEditObject']);
        $order = oxNew(Order::class);
        $order->load($orderId);

        $controller->expects($this->any())->method('getEditObject')->willReturn($order);

        $this->assertSame(
            $controller->isAdyenOrder(),
            Module::isAdyenPayment($orderData['oxorder__oxpaymenttype'])
        );
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testGetEditObject($orderId, $orderData): void
    {
        $controller = $this->createPartialMock(AdminOrderController::class, ['getEditObjectId']);
        $controller->expects($this->any())->method('getEditObjectId')->willReturn($orderId);

        $order = oxNew(Order::class);
        $order->load($orderId);

        $this->assertInstanceOf(Order::class, $controller->getEditObject());
        $this->assertSame($orderId, $controller->getEditObject()->getId());
    }

    public function testGetEmptyEditObject(): void
    {
        $controller = $this->createPartialMock(AdminOrderController::class, ['getEditObjectId']);
        $controller->expects($this->any())->method('getEditObjectId')->willReturn(null);

        $this->assertInstanceOf(Order::class, $controller->getEditObject());
        $this->assertSame(null, $controller->getEditObject()->getId());
    }

    public function providerTestOrderData(): array
    {
        $providerData = [
            [
                '456',
                [
                    'oxorder__oxpaymenttype' => 'dummy'
                ]
            ]
        ];
        $count = 123;
        foreach (Module::PAYMENT_DEFINTIONS as $paymentId => $paymentDef) {
            $count++;
            $providerData[] = [
                (string)$count,
                [
                    'oxorder__oxpaymenttype' => $paymentId,
                    'oxorder__adyenpspreference' => 'test' . $count
                ]
            ];
        }

        return $providerData;
    }
}
