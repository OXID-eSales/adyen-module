<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use OxidEsales\Eshop\Application\Model\Order as eShopOrder;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\PayPalApi\Exception\ApiException;

/**
 * @extendable-class
 */
class Payment
{
    public const PAYMENT_ERROR_NONE = 'ADYEN_PAYMENT_ERROR_NONE';
    public const PAYMENT_ERROR_GENERIC = 'ADYEN_PAYMENT_ERROR_GENERIC';

    private string $executionError = self::PAYMENT_ERROR_NONE;

    private ?object $paymentResult = null;

    /** @var Session */
    private Session $session;

    /** @var Context */
    private Context $context;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponsePayments */
    private AdyenAPIResponsePayments $APIResponse;

    public function __construct(
        Session $session,
        Context $context,
        UserRepository $userRepository,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $APIResponse
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->userRepository = $userRepository;
        $this->moduleSettings = $moduleSettings;
        $this->APIResponse = $APIResponse;
    }

    public function getSessionPaymentId(): string
    {
        return $this->session->getBasket()->getPaymentId();
    }

    public function setPaymentExecutionError(string $text): void
    {
        $this->executionError = $text;
    }

    public function getPaymentExecutionError(): string
    {
        return $this->executionError;
    }

    public function setPaymentResult(object $paymentResult): void
    {
        $this->paymentResult = $paymentResult;
    }

    /**
     * @return mixed
     */
    public function getPaymentResult()
    {
        return $this->paymentResult;
    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     */
    public function doAdyenPayment($amount, $order): bool
    {
        $result = false;

        /** @var Order $order */
        $reference = $order->createNumberForAdyenPayment();

        $paymentState = json_decode($this->session->getVariable(Module::ADYEN_SESSION_PAYMENTSTATEDATA_NAME));

        $currencyDecimals = $this->context->getActiveCurrencyDecimals();
        $decimalFactor = (int)('1' . str_repeat('0', $currencyDecimals));
        $currencyAmountInt = $amount * $decimalFactor;
        $currencyAmount = (string)$currencyAmountInt;

        $payments = oxNew(AdyenAPIPayments::class);
        $payments->setCurrencyName($this->context->getActiveCurrencyName());
        $payments->setReference($reference);
        $payments->setPaymentMethod($paymentState ?: []);
        $payments->setCurrencyAmount($currencyAmount);
        $payments->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $payments->setReturnUrl($this->context->getPaymentReturnUrl());
        $payments->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $payments->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);

        try {
            $result = $this->APIResponse->getPayments($payments);
            $this->setPaymentResult($result);
            $result = true;
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getPayments call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }
}
