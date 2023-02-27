<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Model\Payment as PaymentModel;

/**
 * @extendable-class
 */
class Payment extends PaymentBase
{
    use AdyenPayment;

    private array $paymentResult = [];

    private Context $context;

    private ModuleSettings $moduleSettings;

    private AdyenAPIResponsePayments $APIPayments;

    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $APIPayments
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->APIPayments = $APIPayments;
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
     * @param array $paymentState
     */
    public function collectPayments(float $amount, string $reference, array $paymentState): bool
    {
        $result = false;

        $payments = oxNew(AdyenAPIPayments::class);
        $payments->setCurrencyName($this->context->getActiveCurrencyName());
        $payments->setReference($reference);
        $payments->setPaymentMethod($paymentState['paymentMethod'] ?? []);
        $payments->setOrigin($paymentState['origin'] ?? '');
        $payments->setBrowserInfo($paymentState['browserInfo'] ?? []);
        $payments->setShopperEmail($paymentState['shopperEmail'] ?? '');
        $payments->setShopperIP($paymentState['shopperIP'] ?? '');
        $payments->setCurrencyAmount($this->getAdyenAmount(
            $amount,
            $this->context->getActiveCurrencyDecimals()
        ));
        $payments->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $payments->setReturnUrl($this->context->getPaymentReturnUrl());
        $payments->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $payments->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);
        $payments->setPlatformName(Module::MODULE_PLATFORM_NAME);
        $payments->setPlatformVersion(Module::MODULE_PLATFORM_VERSION);
        $payments->setPlatformIntegrator(Module::MODULE_PLATFORM_INTEGRATOR);

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

    public function supportsCurrency(string $currency, string $paymentId): bool
    {
        // no supported_currencies field means all currency are supported
        if (!isset(ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['supported_currencies'])) {
            return true;
        }

        return in_array($currency, ModuleCore::PAYMENT_DEFINTIONS[$paymentId]['supported_currencies']);
    }

    public function filterNonSupportedCurrencies(array &$payments, string $actualCurrency): array
    {
        return array_filter(
            $payments,
            function (PaymentModel $payment) use ($actualCurrency) {
                return $this->supportsCurrency($actualCurrency, $payment->getId());
            }
        );
    }
}
