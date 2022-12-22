<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\AdyenException;
use Exception;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class PaymentMethods
{
    use AdyenPayment;

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponsePaymentMethods $APIPaymentMethods;

    private UserRepository $userRepository;

    private CountryRepository $countryRepository;

    private ?array $paymentMethods = null;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePaymentMethods $APIPaymentMethods,
        UserRepository $userRepository,
        CountryRepository $countryRepository
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIPaymentMethods = $APIPaymentMethods;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @throws AdyenException
     */
    public function collectAdyenPaymentMethods(): AdyenAPIResponsePaymentMethods
    {
        $paymentMethods = oxNew(AdyenAPIPaymentMethods::class);
        $paymentMethods->setCurrencyName($this->context->getActiveCurrencyName());

        $currencyDecimals = $this->context->getActiveCurrencyDecimals();
        $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
        $paymentMethods->setCurrencyFilterAmount($currencyFilterAmount);
        $paymentMethods->setCountryCode($this->countryRepository->getCountryIso());
        $paymentMethods->setShopperLocale($this->userRepository->getUserLocale());
        $paymentMethods->setMerchantAccount($this->moduleSettings->getMerchantAccount());

        $this->APIPaymentMethods->loadAdyenPaymentMethods($paymentMethods);

        return $this->APIPaymentMethods;
    }

    /**
     * return array with PaymentMethods (array)
     * @throws AdyenException
     * @throws Exception
     */
    public function getAdyenPaymentMethods(): array
    {
        if (is_null($this->paymentMethods)) {
            $paymentMethods = $this->collectAdyenPaymentMethods();
            $this->paymentMethods = $paymentMethods->getAdyenPaymentMethods();
        }
        return $this->paymentMethods;
    }
}