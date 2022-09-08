<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\StaticContents;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

final class StaticContentsTest extends UnitTestCase
{
    use ServiceContainer;

    public function testExistingPaymentsAreNotChanged(): void
    {
        $payment = oxNew(EshopModelPayment::class);
        if (!$payment->loadInLang(0, Module::STANDARD_PAYMENT_ID)) {
            $payment->setId(Module::STANDARD_PAYMENT_ID);
            $payment->setLanguage(0);
        }
        $payment->assign(
            [
                'oxdesc' => 'test_desc_de',
                'oxlongdesc' => 'test_longdesc_de'
            ]
        );
        $payment->save();

        $service = $this->getServiceFromContainer(StaticContents::class);
        $service->ensurePaymentMethods();

        $payment = oxNew(EshopModelPayment::class);
        $payment->loadInLang(0, Module::STANDARD_PAYMENT_ID);
        $this->assertEquals('test_desc_de', $payment->getFieldData('oxdesc'));
        $this->assertEquals('test_longdesc_de', $payment->getFieldData('oxlongdesc'));
    }

    public function testEnsurePaymentMethods(): void
    {
        $paymentIds = array_keys(Module::PAYMENT_DEFINTIONS);

        //clean up before test
        foreach ($paymentIds as $paymentId) {
            $payment = oxNew(EshopModelPayment::class);
            $payment->load($paymentId);
            $payment->delete();
        }

        $service = $this->getServiceFromContainer(StaticContents::class);
        $service->ensurePaymentMethods();

        foreach ($paymentIds as $paymentId) {
            $payment = oxNew(EshopModelPayment::class);
            $this->assertTrue($payment->load($paymentId));

            $payment->loadInLang(0, $paymentId);
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions']['de']['desc'],
                $payment->getRawFieldData('oxdesc')
            );
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions']['de']['longdesc'],
                $payment->getRawFieldData('oxlongdesc')
            );

            $payment->loadInLang(1, $paymentId);
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions']['en']['desc'],
                $payment->getRawFieldData('oxdesc')
            );
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions']['en']['longdesc'],
                $payment->getRawFieldData('oxlongdesc')
            );
        }
    }
}
