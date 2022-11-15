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
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseSession;
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

    protected ?AdyenAPIResponseSession $session = null;
    protected ?AdyenAPIResponsePaymentMethods $paymentMethods = null;

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionId(): string
    {
        $response = $this->getAdyenSessionResponse();
        return $response->getAdyenSessionId();
    }

    /**
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenSessionData(): string
    {
        $response = $this->getAdyenSessionResponse();
        return $response->getAdyenSessionData();
    }

    /**
     * return a JSON-String with PaymentMethods (array)
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenPaymentMethods(): string
    {
        $paymentMethodsData = $this->getAdyenPaymentMethodsData();
        $result = json_encode($paymentMethodsData->getAdyenPaymentMethods());
        return $result ?: '';
    }

    public function getAdyenShopperLocale(): string
    {
        $userRepository = $this->getServiceFromContainer(UserRepository::class);
        return $userRepository->getUserLocale();
    }

    /**
     * @throws \Adyen\AdyenException
     */
    protected function getAdyenSessionResponse(): AdyenAPIResponseSession
    {
        if (is_null($this->session)) {
            $adyenAPISession = oxNew(AdyenAPISession::class);

            $context = $this->getServiceFromContainer(Context::class);
            $userRepository = $this->getServiceFromContainer(UserRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $response = $this->getServiceFromContainer(AdyenAPIResponseSession::class);

            $adyenAPISession->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $adyenAPISession->setCurrencyFilterAmount($currencyFilterAmount);

            $adyenAPISession->setCountryCode($userRepository->getUserCountryIso());

            $adyenAPISession->setShopperLocale($userRepository->getUserLocale());

            $adyenAPISession->setMerchantAccount($moduleSettings->getMerchantAccount());

            $adyenAPISession->setReference(Module::ADYEN_ORDER_REFERENCE_ID);

            $adyenAPISession->setReturnUrl($context->getCurrentShopUrl() . 'index.php?cl=order');

            $response->loadAdyenSession($adyenAPISession);
            $this->session = $response;
        }
        return $this->session;
    }

    /**
     * @throws \Adyen\AdyenException
     */
    public function getAdyenPaymentMethodsData(): AdyenAPIResponsePaymentMethods
    {
        if (is_null($this->paymentMethods)) {
            $paymentMethods = oxNew(AdyenAPIPaymentMethods::class);

            $context = $this->getServiceFromContainer(Context::class);
            $userRepository = $this->getServiceFromContainer(UserRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $response = $this->getServiceFromContainer(AdyenAPIResponsePaymentMethods::class);

            $paymentMethods->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $paymentMethods->setCurrencyFilterAmount($currencyFilterAmount);

            $paymentMethods->setCountryCode($userRepository->getUserCountryIso());

            $paymentMethods->setShopperLocale($userRepository->getUserLocale());

            $paymentMethods->setMerchantAccount($moduleSettings->getMerchantAccount());

            $response->loadAdyenPaymentMethods($paymentMethods);
            $this->paymentMethods = $response;
        }
        return $this->paymentMethods;
    }
}
