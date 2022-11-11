<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Application\Model\Order as eShopOrder;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\Order;

/**
 * @extendable-class
 */
class Payment
{
    public const PAYMENT_ERROR_NONE = 'ADYEN_PAYMENT_ERROR_NONE';
    public const PAYMENT_ERROR_REDIRECT = 'ADYEN_REDIRECT';

    /** @var string */
    private $paymentExecutionError = self::PAYMENT_ERROR_NONE;

    /** @var Session */
    private Session $session;

    /** @var Context */
    private Context $context;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponsePayments */
    private AdyenAPIResponsePayments $adyenAPIResponsePayments;

    public function __construct(
        Session $session,
        Context $context,
        UserRepository $userRepository,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $adyenAPIResponsePayments
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->userRepository = $userRepository;
        $this->moduleSettings = $moduleSettings;
        $this->adyenAPIResponsePayments = $adyenAPIResponsePayments;
    }

    /**
     * Return the PaymentId from session basket
     */
    public function getSessionPaymentId(): string
    {
        return $this->session->getBasket()->getPaymentId();
    }

    public function setPaymentExecutionError(string $text): void
    {
        $this->paymentExecutionError = $text;
    }

    public function getPaymentExecutionError(): string
    {
        return $this->paymentExecutionError;
    }

    public function doAdyenPayment(eShopOrder $order): bool
    {
        /** @var Order $order */
        $reference = $order->createNumberForAdyenPayment();

        $paymentState = json_decode($this->session->getVariable(Module::ADYEN_SESSION_PAYMENTSTATEDATA_NAME));

        $currencyDecimals = $this->context->getActiveCurrencyDecimals();
        $basketAmount = $this->session->getBasket()->getPrice()->getPrice();
        $decimalFactor = (int)('1' . str_repeat('0', $currencyDecimals));
        $currencyAmountInt = $basketAmount * $decimalFactor;
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

        $result = $this->adyenAPIResponsePayments->getPayments($payments);

    }


    public function trackAdyenOrder(
    ): AdyenHistory {
    }
}
