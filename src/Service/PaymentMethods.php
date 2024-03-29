<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\AdyenException;
use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class PaymentMethods extends PaymentBase
{
    use AdyenPayment;

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponsePaymentMethods $APIPaymentMethods;

    private UserRepository $userRepository;

    private CountryRepository $countryRepository;

    private ?array $paymentMethods = null;
    private OxNewService $oxNewService;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePaymentMethods $APIPaymentMethods,
        UserRepository $userRepository,
        CountryRepository $countryRepository,
        OxNewService $oxNewService
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIPaymentMethods = $APIPaymentMethods;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
        $this->oxNewService = $oxNewService;
    }

    public function collectAdyenPaymentMethods(): AdyenAPIResponsePaymentMethods
    {
        $paymentMethods = $this->oxNewService->oxNew(AdyenAPIPaymentMethods::class);
        $paymentMethods->setCurrencyName($this->context->getActiveCurrencyName());

        $currencyDecimals = $this->context->getActiveCurrencyDecimals();
        $currencyFilterAmount = '10' . str_repeat('0', $currencyDecimals);
        $paymentMethods->setCurrencyFilterAmount($currencyFilterAmount);
        $paymentMethods->setCountryCode($this->countryRepository->getCountryIso());
        $paymentMethods->setShopperLocale($this->userRepository->getUserLocale());
        $paymentMethods->setMerchantAccount($this->moduleSettings->getMerchantAccount());

        try {
            $this->APIPaymentMethods->loadAdyenPaymentMethods($paymentMethods);
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on loadAdyenPaymentMethods call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }

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
