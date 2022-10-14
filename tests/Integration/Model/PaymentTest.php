<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Model;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

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
    public function testIsAdyenPaymentAndCheckCapture($paymentId, $isSeperateCapture): void
    {
        $payment = oxNew(Payment::class);
        $payment->load($paymentId);
        $this->assertSame(isset(Module::PAYMENT_DEFINTIONS[$paymentId]), $payment->isAdyenPayment());

        $this->assertSame(
            (
                isset(Module::PAYMENT_DEFINTIONS[$paymentId]) &&
                Module::PAYMENT_DEFINTIONS[$paymentId]['seperatecapture'] &&
                $isSeperateCapture
            ),
            $payment->isAdyenSeperateCapture()
        );
    }

    public function providerTestPaymentData(): array
    {
        $moduleSettings = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettings::class);

        $paymentData = [
            [
                'dummy',
                false
            ]
        ];

        foreach (array_keys(Module::PAYMENT_DEFINTIONS) as $paymentId) {
            $paymentData[] = [
                $paymentId,
                $moduleSettings->isSeperateCapture($paymentId)
            ];
        }

        return $paymentData;
    }
}
