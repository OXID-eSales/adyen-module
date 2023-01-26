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
            $payment->assign([
                'oxpayment__oxactive' => true
            ]);
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
    public function testIsAdyenPaymentAndCheckCapture($paymentId, $isManualCapture, $isImmediateCapture): void
    {
        // Check:  isAdyenPayment
        $payment = oxNew(Payment::class);
        $payment->load($paymentId);
        $this->assertSame(isset(Module::PAYMENT_DEFINTIONS[$paymentId]), $payment->isAdyenPayment());

        // Check: showInPaymentCtrl
        $isActive = $payment->getFieldData('oxactive') === '1' ;
        $this->assertSame(
            (
                isset(Module::PAYMENT_DEFINTIONS[$paymentId]) &&
                Module::PAYMENT_DEFINTIONS[$paymentId]['paymentCtrl'] &&
                $isActive
            ),
            $payment->showInPaymentCtrl()
        );

        // Check: showInOrderCtrl
        $isActive = $payment->getFieldData('oxactive') === '1' ;
        $this->assertSame(
            (
                isset(Module::PAYMENT_DEFINTIONS[$paymentId]) &&
                !Module::PAYMENT_DEFINTIONS[$paymentId]['paymentCtrl'] &&
                $isActive
            ),
            $payment->showInOrderCtrl()
        );

        // Check isAdyenManualCapture
        $this->assertSame(
            (
                isset(Module::PAYMENT_DEFINTIONS[$paymentId]) &&
                Module::PAYMENT_DEFINTIONS[$paymentId]['capturedelay'] &&
                $isManualCapture
            ),
            $payment->isAdyenManualCapture()
        );
        // Check isAdyenImmediateCapture
        $this->assertSame(
            (
                isset(Module::PAYMENT_DEFINTIONS[$paymentId]) &&
                Module::PAYMENT_DEFINTIONS[$paymentId]['capturedelay'] &&
                $isImmediateCapture
            ),
            $payment->isAdyenImmediateCapture()
        );

        $payment->assign([
            'oxpayment__oxactive' => true
        ]);
        $payment->save();
    }

    public function providerTestPaymentData(): array
    {
        /** @var ModuleSettings $moduleSettings */
        $moduleSettings = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettings::class);

        $paymentData = [
            [
                'dummy',
                false,
                false
            ]
        ];

        foreach (array_keys(Module::PAYMENT_DEFINTIONS) as $paymentId) {
            $paymentData[] = [
                $paymentId,
                $moduleSettings->isManualCapture($paymentId),
                $moduleSettings->isImmediateCapture($paymentId)
            ];
        }

        return $paymentData;
    }
}
