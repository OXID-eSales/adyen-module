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
use OxidSolutionCatalysts\Adyen\Traits\RequestGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\UserAddress;

class PaymentController extends PaymentController_parent
{
    use ServiceContainer;
    use UserAddress;
    use RequestGetter;

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
            $state = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PAYMENTSTATEDATA_NAME);
            $pspReference = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME);
            $resultCode = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_RESULTCODE_NAME);
            $amountCurrency = $this->getStringRequestData(Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME);
            $session->setPaymentState($state);
            $session->setPspReference($pspReference);
            $session->setResultCode($resultCode);
            $session->setAmountCurrency($amountCurrency);
        }
        return $result;
    }
}
