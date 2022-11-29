<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 * @extendable-class
 */
class SessionSettings
{
    public const ADYEN_SESSION_PAYMENTMETHODS_NAME = 'sess_adyen_payment_methods';
    public const ADYEN_SESSION_PAYMENTSTATEDATA_NAME = 'sess_adyen_paymentstatedata';
    public const ADYEN_SESSION_REDIRECTLINK_NAME = 'sess_adyen_redirectlink';

    /** @var Session */
    private Session $session;

    public function __construct(
        Session $session
    ) {
        $this->session = $session;
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

    public function setPaymentState(string $paymentState): void
    {
        $this->saveSettingValue(self::ADYEN_SESSION_PAYMENTSTATEDATA_NAME, $paymentState);
    }

    /**
     * @throws \JsonException
     */
    public function getPaymentState(): array
    {
        /** @var null|string $paymentStateJson */
        $paymentStateJson = $this->getSettingValue(self::ADYEN_SESSION_PAYMENTSTATEDATA_NAME);
        $paymentStateJson = $paymentStateJson ?? '';
        $paymentState = json_decode($paymentStateJson, true, 512, JSON_THROW_ON_ERROR);
        return is_array($paymentState) ? $paymentState : [];
    }

    public function deletePaymentState(): void
    {
        $this->removeSettingValue(self::ADYEN_SESSION_PAYMENTSTATEDATA_NAME);
    }

    public function getDeliveryId(): string
    {
        /** @var null|string $addressId */
        $addressId = $this->getSettingValue('deladrid');
        return $addressId ?? '';
    }

    public function getUser(): User
    {
        $user = $this->session->getUser();
        return $user ?: oxNew(User::class);
    }

    public function getPaymentId(): string
    {
        /** @var null|string $paymentId */
        $paymentId = $this->getSettingValue('paymentid');
        if (is_null($paymentId)) {
            /** @var null|Basket $basket */
            $basket = $this->session->getBasket();
            $paymentId = !is_null($basket) ? $basket->getPaymentId() : '';
        }

        return $paymentId;
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
