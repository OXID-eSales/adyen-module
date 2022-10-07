<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;

class PaymentTest extends UnitTestCase
{
    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestPaymentData() as $dataSet) {
            [$paymentId] = $dataSet;
            $payment = oxNew(Payment::class);
            $payment->setId($paymentId);
            $payment->save();
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestPaymentData() as $dataSet) {
            [$paymentId] = $dataSet;
            $payment = oxNew(Payment::class);
            $payment->load($paymentId);
            $payment->delete();
        }
    }

    /**
     * @dataProvider providerTestPaymentData
     */
    public function testIsAdyenPayment($paymentId): void
    {
        $payment = oxNew(Payment::class);
        $payment->load($paymentId);
        $isAdyenPayment = $paymentId === Module::STANDARD_PAYMENT_ID;
        $this->assertSame($isAdyenPayment, $payment->isAdyenPayment());
    }

    public function providerTestPaymentData(): array
    {
        return [
            [
                Module::STANDARD_PAYMENT_ID
            ],
            [
                'dummy'
            ]
        ];
    }
}