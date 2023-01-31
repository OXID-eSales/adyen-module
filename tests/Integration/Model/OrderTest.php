<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Tests\Integration\Traits\Setting;

class OrderTest extends UnitTestCase
{
    use Setting;

    private const PAYMENT_DESC_DUMMY = 'TestDummy';
    private const PAYMENT_ID_DUMMY = 'TestId';

    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestOrderData() as $dataSet) {
            [$orderId, $orderData, , , ] = $dataSet;
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
            [$orderId, , , , ] = $dataSet;
            $order = oxNew(Order::class);
            $order->load($orderId);
            $order->assign(['oxorder__oxpaymenttype' => 'pleaseDelete']);
            $order->save();
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
    public function testIsAdyenOrder($orderId, $orderData): void
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
    public function testIsAdyenOrderPaid($orderId, $orderData): void
    {
        $order = oxNew(Order::class);
        $order->load($orderId);
        $isAdyenOrderPaid = (
            'OK' === $orderData['oxorder__oxtransstatus'] &&
            !str_contains($orderData['oxorder__oxpaid'], '0000')
        );

        $this->assertSame($isAdyenOrderPaid, $order->isAdyenOrderPaid());
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testGetAdyenPaymentName($orderId, $orderData, $paymentId, $paymentName): void
    {
        $orderMock = $this->getMockBuilder(Order::class)
            ->onlyMethods(['isAdyenOrder'])
            ->getMock();

        $orderMock->method('isAdyenOrder')
            ->willReturn(true);

        $orderMock->load($orderId);

        $this->assertSame($paymentName, $orderMock->getAdyenPaymentName());
    }

    /**
     * @dataProvider providerTestOrderData
     */
    public function testIsAdyenManualCapture($orderId, $orderData, $paymentId, $paymentName, $paymentCapture): void
    {
        $config = Registry::getConfig();
        $orderMock = $this->getMockBuilder(Order::class)
            ->onlyMethods(['isAdyenOrder'])
            ->getMock();

        $orderMock->method('isAdyenOrder')
            ->willReturn(true);

        $orderMock->load($orderId);
        if ('isNotExists' !== $config->getConfigParam(
            ModuleSettings::CAPTURE_DELAY . $paymentId,
            'isNotExists'
        )) {
            $this->updateModuleSetting(ModuleSettings::CAPTURE_DELAY . $paymentId, $paymentCapture);
        }

        $isAdyenManualCapture = $paymentCapture === $config->getConfigParam(
            ModuleSettings::CAPTURE_DELAY . $paymentId,
            'dummy'
        );
        $this->assertSame($isAdyenManualCapture, $orderMock->isAdyenManualCapture());
    }

    public function providerTestOrderData(): array
    {
        $providerData = [
            [
                '456',
                [
                    'oxorder__oxpaymenttype' => 'dummy',
                    'oxorder__transstatus' => 'OK',
                    'oxorder__paid' => '2023-01-01 00:00:00'
                ],
                self::PAYMENT_ID_DUMMY,
                self::PAYMENT_DESC_DUMMY,
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
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
                $paymentId,
                $paymentDef['descriptions']['de']['desc'],
                Module::ADYEN_CAPTURE_DELAY_MANUAL
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
