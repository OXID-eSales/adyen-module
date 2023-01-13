<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Traits\Json;

/**
 * @extendable-class
 */
class SessionSettings
{
    use Json;
    use AdyenPayment;

    public const ADYEN_SESSION_ORDER_REFERENCE = 'sess_adyen_order_reference';
    public const ADYEN_SESSION_PAYMENTMETHODS_NAME = 'sess_adyen_payment_methods';
    public const ADYEN_SESSION_PSPREFERENCE_NAME = 'sess_adyen_pspreference';
    public const ADYEN_SESSION_RESULTCODE_NAME = 'sess_adyen_resultcode';
    public const ADYEN_SESSION_AMOUNTVALUE_NAME = 'sess_adyen_amountvalue';
    public const ADYEN_SESSION_AMOUNTCURRENCY_NAME = 'sess_adyen_amountcurrency';
    public const ADYEN_SESSION_REDIRECTLINK_NAME = 'sess_adyen_redirectlink';

    /** @var Session */
    private Session $session;

    private Context $context;

    public function __construct(
        Session $session,
        Context $context
    ) {
        $this->session = $session;
        $this->context = $context;
    }

    public function setRedirctLink(string $redirectLink): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_REDIRECTLINK_NAME, $redirectLink);
    }

    public function getRedirctLink(): string
    {
        /** @var null|string $redirectLink */
        $redirectLink = $this->getSettingValue(self::ADYEN_SESSION_REDIRECTLINK_NAME);
        return $redirectLink ?? '';
    }

    public function deleteRedirctLink(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_REDIRECTLINK_NAME);
    }

    public function createOrderReference(): string
    {
        $orderReference = Registry::getUtilsObject()->generateUId();
        $this->saveSettingValue(self::ADYEN_SESSION_ORDER_REFERENCE, $orderReference);
        return $orderReference;
    }

    public function getOrderReference(): string
    {
        /** @var null|string $orderReference */
        $orderReference = $this->getSettingValue(self::ADYEN_SESSION_ORDER_REFERENCE);
        return $orderReference ?? '';
    }

    public function deleteOrderReference(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_ORDER_REFERENCE);
    }

    public function setPaymentMethods(array $paymentMethods): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_PAYMENTMETHODS_NAME, $paymentMethods);
    }

    public function getPaymentMethods(): array
    {
        /** @var null|array $adyenPaymentMethods */
        $adyenPaymentMethods = $this->getSettingValue(self::ADYEN_SESSION_PAYMENTMETHODS_NAME);
        return $adyenPaymentMethods ?? [];
    }

    public function deletePaymentMethods(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_PAYMENTMETHODS_NAME);
    }

    public function setPspReference(string $pspReference): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_PSPREFERENCE_NAME, $pspReference);
    }

    public function getPspReference(): string
    {
        /** @var null|string $pspReference */
        $pspReference = $this->getSettingValue(self::ADYEN_SESSION_PSPREFERENCE_NAME);
        return $pspReference ?? '';
    }

    public function deletePspReference(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_PSPREFERENCE_NAME);
    }

    public function setResultCode(string $resultCode): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_RESULTCODE_NAME, $resultCode);
    }

    public function getResultCode(): string
    {
        /** @var null|string $resultCode */
        $resultCode = $this->getSettingValue(self::ADYEN_SESSION_RESULTCODE_NAME);
        return $resultCode ?? '';
    }

    public function deleteResultCode(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_RESULTCODE_NAME);
    }

    public function setAmountValue(float $amountValue): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_AMOUNTVALUE_NAME, $amountValue);
    }

    public function getAmountValue(): float
    {
        /** @var null|float $amountValue */
        $amountValue = $this->getSettingValue(self::ADYEN_SESSION_AMOUNTVALUE_NAME);
        return $amountValue ?? 0.0;
    }

    public function deleteAmountValue(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_AMOUNTVALUE_NAME);
    }

    public function setAmountCurrency(string $amountCurrency): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_AMOUNTCURRENCY_NAME, $amountCurrency);
    }

    public function getAmountCurrency(): string
    {
        /** @var null|string $amountCurrency */
        $amountCurrency = $this->getSettingValue(self::ADYEN_SESSION_AMOUNTCURRENCY_NAME);
        return $amountCurrency ?? '';
    }

    public function deleteAmountCurrency(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_AMOUNTCURRENCY_NAME);
    }

    public function deletePaymentSession(): void
    {
        $this->deletePspReference();
        $this->deleteOrderReference();
        $this->deleteAmountCurrency();
        $this->deleteAmountValue();
    }

    public function getDeliveryId(): string
    {
        /** @var null|string $addressId */
        $addressId = $this->getSettingValue('deladrid');
        return $addressId ?? '';
    }

    public function getUser(): User
    {
        return $this->session->getUser();
    }

    public function getPaymentId(): string
    {
        /** @var null|string $paymentId */
        $paymentId = $this->getSettingValue('paymentid');
        if (is_null($paymentId)) {
            /** @var null|Basket $basket */
            $basket = $this->session->getBasket();
            $paymentId = !is_null($basket) && $basket->getPaymentId() ? $basket->getPaymentId() : '';
        }

        return $paymentId;
    }

    public function getAdyenBasketAmount(): float
    {
        /** @var null|Basket $basket */
        $basket = $this->session->getBasket();
        $basketOxidAmount = !is_null($basket) && $basket->getPrice() ? $basket->getPrice()->getBruttoPrice() : 0.0;
        return (float)$this->getAdyenAmount(
            $basketOxidAmount,
            $this->context->getActiveCurrencyDecimals()
        );
    }

    /**
     * @param string $key
     * @param bool|int|string|array $value
     */
    private function saveSettingValue($key, $value): void
    {
        $this->session->setVariable($key, $value);
    }

    /**
     * @return mixed
     */
    private function getSettingValue(string $key)
    {
        return $this->session->getVariable($key);
    }

    private function removeSettingValue(string $key): void
    {
        $this->session->deleteVariable($key);
    }
}
