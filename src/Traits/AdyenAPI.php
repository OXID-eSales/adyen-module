<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPISessionResponse;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;

/**
 * Convenience trait to fetch Adyen API Services.
 * Use for example in classes where it's not possible to inject services in
 * the constructor because constructor is inherited from a shop core class.
 * Example: see module controllers
 */
trait AdyenAPI
{
    use ServiceContainer;

    protected ?AdyenAPISessionResponse $adyenApiSessionResponse = null;
    protected ?AdyenAPIPaymentMethodsResponse $adyenAPIPaymentMethodsResponse = null;

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionId(): string
    {
        $adyenApiSessionResponse = $this->getAdyenSessionResponse();
        return $adyenApiSessionResponse->getAdyenSessionId();
    }

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionData(): string
    {
        $adyenApiSessionResponse = $this->getAdyenSessionResponse();
        return $adyenApiSessionResponse->getAdyenSessionData();
    }

    /**
     * @throws \Adyen\AdyenException
     */
    protected function getAdyenSessionResponse(): AdyenAPISessionResponse
    {
        if (is_null($this->adyenApiSessionResponse)) {
            $adyenAPISession = oxNew(AdyenAPISession::class);

            $context = $this->getServiceFromContainer(Context::class);
            $userRepository = $this->getServiceFromContainer(UserRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $adyenApiSessionResponse = $this->getServiceFromContainer(AdyenAPISessionResponse::class);

            $adyenAPISession->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $adyenAPISession->setCurrencyFilterAmount($currencyFilterAmount);

            $adyenAPISession->setCountryCode($userRepository->getUserCountryIso());

            $adyenAPISession->setShopperLocale($userRepository->getUserLocale());

            $adyenAPISession->setMerchantAccount($moduleSettings->getMerchantAccount());

            $adyenAPISession->setReference(Module::ADYEN_ORDER_REFERENCE_ID);

            $adyenAPISession->setReturnUrl($context->getCurrentShopUrl() . 'index.php?cl=order');

            $adyenApiSessionResponse->loadAdyenSession($adyenAPISession);
            $this->adyenApiSessionResponse = $adyenApiSessionResponse;
        }
        return $this->adyenApiSessionResponse;
    }

    /**
     * @throws \Adyen\AdyenException
     */
    protected function getAdyenPaymentMethodsResponse(): AdyenAPIPaymentMethodsResponse
    {
        if (is_null($this->adyenAPIPaymentMethods)) {
            $adyenAPIPaymentMethods = oxNew(AdyenAPIPaymentMethods::class);

            $context = $this->getServiceFromContainer(Context::class);
            $userRepository = $this->getServiceFromContainer(UserRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $adyenAPIPaymentMethodsResponse = $this->getServiceFromContainer(AdyenAPIPaymentMethodsResponse::class);

            $adyenAPIPaymentMethods->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $adyenAPIPaymentMethods->setCurrencyFilterAmount($currencyFilterAmount);

            $adyenAPIPaymentMethods->setCountryCode($userRepository->getUserCountryIso());

            $adyenAPIPaymentMethods->setShopperLocale($userRepository->getUserLocale());

            $adyenAPIPaymentMethods->setMerchantAccount($moduleSettings->getMerchantAccount());

            $adyenAPIPaymentMethodsResponse->loadAdyenSession($adyenAPIPaymentMethods);
            $this->adyenAPIPaymentMethodsResponse = $adyenAPIPaymentMethodsResponse;
        }
        return $this->adyenAPIPaymentMethodsResponse;
    }
}
