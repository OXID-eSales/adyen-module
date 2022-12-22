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
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;

/**
 * @extendable-class
 */
class Payment
{
    use AdyenPayment;

    public const PAYMENT_ERROR_NONE = 'ADYEN_PAYMENT_ERROR_NONE';
    public const PAYMENT_ERROR_GENERIC = 'ADYEN_PAYMENT_ERROR_GENERIC';

    private string $executionError = self::PAYMENT_ERROR_NONE;

    private array $paymentResult = [];

    private SessionSettings $session;

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponsePayments $APIPayments;

    private AdyenAPIResponsePaymentMethods $APIPaymentMethods;

    private UserRepository $userRepository;

    private CountryRepository $countryRepository;

    private ?array $paymentMethods = null;

    public function __construct(
        SessionSettings $session,
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $APIPayments,
        AdyenAPIResponsePaymentMethods $APIPaymentMethods,
        UserRepository $userRepository,
        CountryRepository $countryRepository
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIPayments = $APIPayments;
        $this->APIPaymentMethods = $APIPaymentMethods;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
    }

    public function setPaymentExecutionError(string $text): void
    {
        $this->executionError = $text;
    }

    public function getPaymentExecutionError(): string
    {
        return $this->executionError;
    }

    public function setPaymentResult(array $paymentResult): void
    {
        $this->paymentResult = $paymentResult;
    }

    public function getPaymentResult(): array
    {
        return $this->paymentResult;
    }

    /**
     * @param double $amount Goods amount
     * @param string $reference Unique Order-Reference
     */
    public function doAdyenAuthorization(float $amount, string $reference): bool
    {
        $paymentState = $this->session->getPaymentState();
        // not necessary anymore, so cleanup
        $this->session->deletePaymentState();

        return $this->collectPayments($amount, $reference, $paymentState);
    }

    /**
     * @param double $amount Goods amount
     * @param string $reference Unique Order-Reference
     * @param array $paymentMethod
     */
    public function collectPayments(float $amount, string $reference, array $paymentMethod): bool
    {
        $result = false;

        $payments = oxNew(AdyenAPIPayments::class);
        $payments->setCurrencyName($this->context->getActiveCurrencyName());
        $payments->setReference($reference);
        $payments->setPaymentMethod($paymentMethod);
        $payments->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $payments->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $payments->setReturnUrl($this->context->getPaymentReturnUrl());
        $payments->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $payments->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);

        try {
            $resultPayments = $this->APIPayments->getPayments($payments);
            if (is_array($resultPayments)) {
                $this->setPaymentResult($resultPayments);
                $result = true;
            }
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getPayments call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
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
