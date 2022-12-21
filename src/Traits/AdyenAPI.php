<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
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
    use Json;

    protected ?AdyenAPIResponsePaymentMethods $paymentMethods = null;

    /**
     * return a JSON-String with PaymentMethods (array)
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    public function getAdyenPaymentMethods(): string
    {
        return $this->arrayToJson($this->getAdyenPaymentMethodsRaw());
    }

    public function existsAdyenPaymentMethods(): bool
    {
        return (bool)count($this->getAdyenPaymentMethodsRaw());
    }

    public function getAdyenShopperLocale(): string
    {
        return $this->getServiceFromContainer(UserRepository::class)->getUserLocale();
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
            $countryRepository = $this->getServiceFromContainer(CountryRepository::class);
            $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
            $response = $this->getServiceFromContainer(AdyenAPIResponsePaymentMethods::class);

            $paymentMethods->setCurrencyName($context->getActiveCurrencyName());

            $currencyDecimals = $context->getActiveCurrencyDecimals();
            $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
            $paymentMethods->setCurrencyFilterAmount($currencyFilterAmount);

            $paymentMethods->setCountryCode($countryRepository->getCountryIso());

            $paymentMethods->setShopperLocale($userRepository->getUserLocale());

            $paymentMethods->setMerchantAccount($moduleSettings->getMerchantAccount());

            $response->loadAdyenPaymentMethods($paymentMethods);
            $this->paymentMethods = $response;
        }
        return $this->paymentMethods;
    }

    /**
     * return array with PaymentMethods (array)
     * @throws \Adyen\AdyenException
     * @throws \Exception
     */
    protected function getAdyenPaymentMethodsRaw(): array
    {
        $paymentMethodsData = $this->getAdyenPaymentMethodsData();
        return $paymentMethodsData->getAdyenPaymentMethods();
    }
}
