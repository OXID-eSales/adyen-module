<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 * @extendable-class
 */
class ModuleSettings
{
    public const OPERATION_MODE = 'osc_adyen_OperationMode';
    public const LOGGING_ACTIVE = 'osc_adyen_LoggingActive';

    public const SANDBOX_API_KEY = 'osc_adyen_SandboxAPIKey';
    public const SANDBOX_CLIENT_KEY = 'osc_adyen_SandboxClientKey';
    public const SANDBOX_HMAC_SIGNATURE = 'osc_adyen_SandboxHmacSignature';
    public const SANDBOX_MERCHANT_ACCOUNT = 'osc_adyen_SandboxMerchantAccount';
    public const SANDBOX_PAYPAL_MERCHANT_ID = 'osc_adyen_SandboxPayPalMerchantId';
    public const LIVE_API_KEY = 'osc_adyen_LiveAPIKey';
    public const LIVE_CLIENT_KEY = 'osc_adyen_LiveClientKey';
    public const LIVE_ENDPOINT_PREFIX = 'osc_adyen_LiveEndpointPrefix';
    public const LIVE_HMAC_SIGNATURE = 'osc_adyen_LiveHmacSignature';
    public const LIVE_MERCHANT_ACCOUNT = 'osc_adyen_LiveMerchantAccount';
    public const LIVE_PAYPAL_MERCHANT_ID = 'osc_adyen_LivePayPalMerchantId';
    public const CAPTURE_DELAY = 'osc_adyen_CaptureDelay_';

    public const ACTIVE_PAYMENTS = 'osc_adyen_activePayments';

    public const KLARNA_PAYMENT_TYPE = 'osc_adyen_KlarnaPaymentType';

    public const OPERATION_MODE_SANDBOX = 'test';
    public const OPERATION_MODE_LIVE = 'live';

    public const OPERATION_MODE_GOOGLE_PAY_SANDBOX = 'TEST';
    public const OPERATION_MODE_GOOGLE_PAY_PRODUCTION = 'PRODUCTION';

    public const OPERATION_MODE_VALUES = [
        self::OPERATION_MODE_SANDBOX,
        self::OPERATION_MODE_LIVE,
    ];

    /** @var ModuleSettingBridgeInterface */
    private $moduleSettingBridge;

    public function __construct(
        ModuleSettingBridgeInterface $moduleSettingBridge
    ) {
        $this->moduleSettingBridge = $moduleSettingBridge;
    }

    public function checkConfigHealth(): bool
    {
        return (
            $this->getAPIKey() &&
            $this->getClientKey() &&
            $this->getMerchantAccount()
        );
    }

    public function isSandBoxMode(): bool
    {
        return self::OPERATION_MODE_SANDBOX === $this->getOperationMode();
    }

    public function getOperationMode(): string
    {
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue(self::OPERATION_MODE);
        $value = (string)$settingValue;

        return (!empty($value) && in_array($value, self::OPERATION_MODE_VALUES)) ?
            $value :
            self::OPERATION_MODE_SANDBOX;
    }

    public function getGooglePayOperationMode(): string
    {
        return $this->getOperationMode() === self::OPERATION_MODE_LIVE ?
            self::OPERATION_MODE_GOOGLE_PAY_PRODUCTION : self::OPERATION_MODE_GOOGLE_PAY_SANDBOX;
    }

    public function getEndPointUrlPrefix(): string
    {
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue(self::LIVE_ENDPOINT_PREFIX);
        $value = (string)$settingValue;

        return !$this->isSandBoxMode() ? $value : '';
    }

    public function isLoggingActive(): bool
    {
        return (bool) $this->getSettingValue(self::LOGGING_ACTIVE);
    }

    public function getAPIKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_API_KEY : self::LIVE_API_KEY);
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue($key);
        return (string)$settingValue;
    }

    public function getClientKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_CLIENT_KEY : self::LIVE_CLIENT_KEY);
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue($key);
        return (string)$settingValue;
    }

    public function getHmacSignature(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_HMAC_SIGNATURE : self::LIVE_HMAC_SIGNATURE);
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue($key);
        return (string)$settingValue;
    }

    public function getMerchantAccount(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_MERCHANT_ACCOUNT : self::LIVE_MERCHANT_ACCOUNT);
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue($key);
        return (string)$settingValue;
    }

    public function getPayPalMerchantId(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_PAYPAL_MERCHANT_ID : self::LIVE_PAYPAL_MERCHANT_ID);
        /** @var null|string $settingValue */
        $settingValue = $this->getSettingValue($key);
        return (string)$settingValue;
    }

    public function isManualCapture(string $paymentId): bool
    {
        return $this->getCaptureDelay($paymentId) === Module::ADYEN_CAPTURE_DELAY_MANUAL;
    }

    public function isImmediateCapture(string $paymentId): bool
    {
        return $this->getCaptureDelay($paymentId) === Module::ADYEN_CAPTURE_DELAY_IMMEDIATE;
    }

    public function getLocaleForCountryIso(string $countryIso): string
    {
        // make sure provided keys and searched keys are in lower case
        $countryIso = strtolower($countryIso);

        /** @var null|array $languageSettings */
        $languageSettings = $this->getSettingValue('osc_adyen_Languages');
        $languageSettings = $languageSettings ?? [];
        $languages = array_change_key_case($languageSettings, CASE_LOWER);
        return isset($languages[$countryIso]) ? (string)$languages[$countryIso] : '';
    }

    public function saveActivePayments(array $activePayments): void
    {
        $this->saveSettingValue(self::ACTIVE_PAYMENTS, $activePayments);
    }

    public function getActivePayments(): array
    {
        return (array)$this->getSettingValue(self::ACTIVE_PAYMENTS);
    }

    public function getKlarnaPaymentType(): string
    {
        return $this->getSettingValue(self::KLARNA_PAYMENT_TYPE);
    }

    private function getCaptureDelay(string $paymentId): string
    {
        /** @var null|string $captureDelay */
        $captureDelay = $this->getSettingValue(self::CAPTURE_DELAY . $paymentId);
        return $captureDelay ?? '';
    }

    /**
     * @param string $key
     * @param bool|int|string|array $value
     */
    private function saveSettingValue($key, $value): void
    {
        $this->moduleSettingBridge->save($key, $value, Module::MODULE_ID);
    }

    /**
     * @return mixed
     */
    private function getSettingValue(string $key)
    {
        return $this->moduleSettingBridge->get($key, Module::MODULE_ID);
    }
}
