<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Model;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Core\Module;

class OrderTest extends UnitTestCase
{
    private const PAYMENT_DESC_DUMMY = 'TestDummy';

    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestOrderData() as $dataSet) {
            [$orderId, $orderData, ] = $dataSet;
            $order = oxNew(Order::class);
            $order->setId($orderId);
            $order->assign($orderData);
            $order->save();
        }
        foreach ($this->providerTestPaymentData() as $dataSet) {
            [$paymentId, $paymentData] = $dataSet;
            $payment = oxNew(Payment::class);
            $payment->setId($paymentId);
            $payment->assign($paymentData);
            $payment->save();
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestOrderData() as $dataSet) {
            [$orderId, , ] = $dataSet;
            $order = oxNew(Order::class);
            $order->load($orderId);
            $order->delete();
        }
        foreach ($this->providerTestPaymentData() as $dataSet) {
            [$paymentId, ] = $dataSet;
            $payment = oxNew(Payment::class);
            $payment->load($paymentId);
            $payment->delete();
        }
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testIsAdyenOrder($orderId, $orderData, $paymentName): void
    {
        $order = oxNew(Order::class);
        $order->load($orderId);
        $isAdyenOrder = (
            Module::isAdyenPayment($orderData['oxorder__oxpaymenttype']) &&
            $orderData['oxorder__adyenpspreference'] !== ''
        );
        $this->assertSame($isAdyenOrder, $order->isAdyenOrder());
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testGetAdyenPaymentName($orderId, $orderData, $paymentName): void
    {
        $order = oxNew(Order::class);
        $order->load($orderId);

        //Module::isAdyenPayment($this->getAdyenOrderData('oxpaymenttype'))
/*
        $moduleMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['isAdyenPayment'])
            ->getMock();
        $moduleMock->method('isAdyenPayment')
            ->willReturn(true);
*/


        $this->assertSame($paymentName, $order->getAdyenPaymentName());
    }

    public function providerTestOrderData(): array
    {
        $providerData = [
            [
                '456',
                [
                    'oxorder__oxpaymenttype' => 'dummy'
                ],
                self::PAYMENT_DESC_DUMMY
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
                ],
                $paymentDef['descriptions']['de']['desc']
            ];
        }

        return $providerData;
    }

    public function providerTestPaymentData(): array
    {
        $providerData = [
            [
                'dummy',
                [
                    'oxpayments__oxdesc' => self::PAYMENT_DESC_DUMMY
                ]
            ]
        ];
        foreach (Module::PAYMENT_DEFINTIONS as $paymentId => $paymentDef) {
            $providerData[] = [
                $paymentId,
                [
                    'oxpayments__oxdesc' => $paymentDef['descriptions']['de']['desc']
                ]
            ];
        }
        return $providerData;
    }
}
