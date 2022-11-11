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
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
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
        $userRepository = $this->getServiceFromContainer(UserRepository::class);
        $userCountryIso = $userRepository->getUserCountryIso();

        $paymentList = [];

        $adyenHealth = $this->getServiceFromContainer(ModuleSettings::class)->checkHealth();

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
                        empty($adyenDef[$key]['currencies']) ||
                        in_array($actShopCurrency->name, $adyenDef[$key]['currencies'], true)
                    ) &&
                    (
                        empty($adyenDef[$key]['countries']) ||
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
        $result = parent::validatePayment();
        $session = Registry::getSession();
        $paymentId = $session->getVariable('paymentid');
        if (Module::isAdyenPayment($paymentId)) {
            $request = oxNew(Request::class);
            $paymentMethodState = $request->getRequestEscapedParameter('adyenStateDataPaymentMethod');
            $session->setVariable('adyenStateDataPaymentMethod', $paymentMethodState);
        }
        return $result;
    }
}
