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
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
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

    private ?array $paymentResult = null;

    private ?array $captureResult = null;

    /** @var Session */
    private Session $session;

    /** @var Context */
    private Context $context;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var AdyenAPIResponsePayments */
    private AdyenAPIResponsePayments $APIPayments;

    /** @var AdyenAPIResponsePayments */
    private AdyenAPIResponseCaptures $APICaptures;

    public function __construct(
        Session $session,
        Context $context,
        UserRepository $userRepository,
        ModuleSettings $moduleSettings,
        AdyenAPIResponsePayments $APIPayments,
        AdyenAPIResponseCaptures $APICaptures
    ) {
        $this->session = $session;
        $this->context = $context;
        $this->userRepository = $userRepository;
        $this->moduleSettings = $moduleSettings;
        $this->APIPayments = $APIPayments;
        $this->APICaptures = $APICaptures;
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

    public function setPaymentResult(array $paymentResult): void
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

    public function setCaptureResult(array $captureResult): void
    {
        $this->captureResult = $captureResult;
    }

    /**
     * @return mixed
     */
    public function getCaptureResult()
    {
        return $this->captureResult;
    }

    /**
     * @param double $amount Goods amount
     * @param eShopOrder $order User ordering object
     */
    public function doAdyenAuthorization(float $amount, eShopOrder $order): bool
    {
        $result = false;

        /** @var Order $order */
        $reference = $order->createNumberForAdyenPayment();

        $paymentState = json_decode($this->session->getVariable(Module::ADYEN_SESSION_PAYMENTSTATEDATA_NAME), true);
        // not necessary anymore, so cleanup
        $this->session->deleteVariable(Module::ADYEN_SESSION_PAYMENTSTATEDATA_NAME);

        $payments = oxNew(AdyenAPIPayments::class);
        $payments->setCurrencyName($this->context->getActiveCurrencyName());
        $payments->setReference($reference);
        $payments->setPaymentMethod($paymentState ?: []);
        $payments->setCurrencyAmount($this->getAdyenAmount($amount));
        $payments->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $payments->setReturnUrl($this->context->getPaymentReturnUrl());
        $payments->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $payments->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);

        try {
            $result = $this->APIPayments->getPayments($payments);
            $this->setPaymentResult($result);
            $result = true;
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on getPayments call.", [$exception]);
            $this->setPaymentExecutionError(self::PAYMENT_ERROR_GENERIC);
        }
        return $result;
    }

    /**
     * @param double $amount Goods amount
     * @param string $pspReference User ordering object
     * @param string $orderNr as unique reference
     */
    public function doAdyenCapture(float $amount, string $pspReference, string $orderNr): bool
    {
        $result = false;

        $captures = oxNew(AdyenAPICaptures::class);
        $captures->setCurrencyName($this->context->getActiveCurrencyName());
        $captures->setReference($orderNr);
        $captures->paymentPspReference($pspReference);
        $captures->setCurrencyAmount($this->getAdyenAmount($amount));
        $captures->setMerchantAccount($this->moduleSettings->getMerchantAccount());
        $captures->setMerchantApplicationName(Module::MODULE_NAME_EN);
        $captures->setMerchantApplicationVersion(Module::MODULE_VERSION_FULL);

        try {
            $result = $this->APICaptures->setCapture($captures);
            $this->setCaptureResult($result);
            $result = true;
        } catch (Exception $exception) {
            Registry::getLogger()->error("Error on setCapture call.", [$exception]);
        }
        return $result;
    }

    protected function getAdyenAmount(float $amount): string {
        $currencyDecimals = $this->context->getActiveCurrencyDecimals();
        $decimalFactor = (int)('1' . str_repeat('0', $currencyDecimals));
        $currencyAmountInt = $amount * $decimalFactor;
        return (string)$currencyAmountInt;
    }
}
