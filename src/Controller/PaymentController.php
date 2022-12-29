<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

class PaymentController extends PaymentController_parent
{
    use ServiceContainer;

    /**
     * Template variable getter. Returns paymentlist
     *
     * @return array<array-key, mixed>|object
     */
    public function getPaymentList()
    {
        /** @var array $paymentListRaw */
        $paymentListRaw = parent::getPaymentList();
        $adyenDef = Module::PAYMENT_DEFINTIONS;
        $actShopCurrency = Registry::getConfig()->getActShopCurrencyObject();
        $countryRepository = $this->getServiceFromContainer(CountryRepository::class);
        $userCountryIso = $countryRepository->getCountryIso();

        $paymentList = [];

        $adyenHealth = $this->getServiceFromContainer(ModuleSettings::class)->checkConfigHealth();

        /*
         * check & allow:
         * - all none Adyen-Payments
         * - adyenHealth
         * - currency
         * - country
         */
        foreach ($paymentListRaw as $key => $payment) {
            if (
                !isset($adyenDef[$key]) ||
                (
                    $adyenHealth &&
                    (
                        empty($adyenDef[$key]['currencies']) || // @phpstan-ignore-line
                        in_array($actShopCurrency->name, $adyenDef[$key]['currencies'], true)
                    ) &&
                    (
                        empty($adyenDef[$key]['countries']) || // @phpstan-ignore-line
                        in_array($userCountryIso, $adyenDef[$key]['countries'], true)
                    )
                )
            ) {
                $paymentList[$key] = $payment;
            }
        }
        return $paymentList;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return  mixed
     */
    public function validatePayment()
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $result = parent::validatePayment();
        $paymentId = $session->getPaymentId();
        if (Module::isAdyenPayment($paymentId)) {
            $request = oxNew(Request::class);
            /** @var null|string $state */
            $state = $request->getRequestParameter(Module::ADYEN_HTMLPARAM_PAYMENTSTATEDATA_NAME);
            $state = $state ?? '';
            $session->setPaymentState($state);
            /** @var null|string $browserInfo */
            $browserInfo = $request->getRequestParameter(Module::ADYEN_HTMLPARAM_BROWSERINFO_NAME);
            $browserInfo = $browserInfo ?? '';
            $session->setBrowserInfo($browserInfo);
        }
        return $result;
    }
}
