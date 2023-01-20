<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\StaticContents;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

final class StaticContentsTest extends UnitTestCase
{
    use ServiceContainer;

    public function testExistingPaymentsAreNotChanged(): void
    {
        $lang = Registry::getLang();
        $langId = $lang->getBaseLanguage();
        $langAbbr = $lang->getLanguageAbbr();

        $payment = oxNew(EshopModelPayment::class);
        if (!$payment->loadInLang($langId, Module::PAYMENT_CREDITCARD_ID)) {
            $payment->setId(Module::PAYMENT_CREDITCARD_ID);
            $payment->setLanguage($langId);
        }
        $descriptions = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions'][$langAbbr];
        $payment->assign(
            [
                'oxdesc' => $descriptions['desc'],
                'oxlongdesc' => $descriptions['longdesc']
            ]
        );
        $payment->save();

        $service = $this->getServiceFromContainer(StaticContents::class);
        $service->ensurePaymentMethods();

        $payment = oxNew(EshopModelPayment::class);
        $payment->loadInLang($langId, Module::PAYMENT_CREDITCARD_ID);
        $this->assertEquals($descriptions['desc'], $payment->getFieldData('oxdesc'));
        $this->assertEquals($descriptions['longdesc'], $payment->getFieldData('oxlongdesc'));
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

            $lang = Registry::getLang();
            $langId = $lang->getBaseLanguage();
            $langAbbr = $lang->getLanguageAbbr($langId);

            $payment->loadInLang($langId, $paymentId);
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions'][ $langAbbr]['desc'],
                $payment->getRawFieldData('oxdesc')
            );
            $this->assertEquals(
                Module::PAYMENT_DEFINTIONS[$paymentId]['descriptions'][ $langAbbr]['longdesc'],
                $payment->getRawFieldData('oxlongdesc')
            );
        }

        /** @var ModuleSettings $moduleSettings */
        $moduleSettings = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettings::class);
        $moduleSettings->saveActivePayments($paymentIds);

        $service->ensurePaymentMethods();

        //check if reactivate
        foreach ($paymentIds as $paymentId) {
            $payment = oxNew(EshopModelPayment::class);
            $payment->load($paymentId);
            $this->assertTrue((bool)$payment->getFieldData('oxactive'));
        }
    }
}
