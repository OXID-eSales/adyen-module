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
    public const SANDBOX_NOTIFICATION_USERNAME = 'osc_adyen_SandboxNotificationUsername';
    public const SANDBOX_NOTIFICATION_PASSWORD = 'osc_adyen_SandboxNotificationPassword';

    public const LIVE_API_KEY = 'osc_adyen_LiveAPIKey';
    public const LIVE_CLIENT_KEY = 'osc_adyen_LiveClientKey';
    public const LIVE_HMAC_SIGNATURE = 'osc_adyen_LiveHmacSignature';
    public const LIVE_MERCHANT_ACCOUNT = 'osc_adyen_LiveMerchantAccount';
    public const LIVE_NOTIFICATION_USERNAME = 'osc_adyen_LiveNotificationUsername';
    public const LIVE_NOTIFICATION_PASSWORD = 'osc_adyen_LiveNotificationPassword';

    public const ACTIVE_PAYMENTS = 'osc_adyen_activePayments';

    public const OPERATION_MODE_SANDBOX = 'test';
    public const OPERATION_MODE_LIVE = 'live';

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

    public function checkHealth(): bool
    {
        return (
            $this->getAPIKey() &&
            $this->getClientKey() &&
            $this->getHmacSignature() &&
            $this->getMerchantAccount() &&
            $this->getNotificationUsername() &&
            $this->getNotificationPassword()
        );
    }

    public function isSandBoxMode(): bool
    {
        return self::OPERATION_MODE_SANDBOX === $this->getOperationMode();
    }

    public function getOperationMode(): string
    {
        $value = (string) $this->getSettingValue(self::OPERATION_MODE);

        return (!empty($value) && in_array($value, self::OPERATION_MODE_VALUES)) ?
            $value :
            self::OPERATION_MODE_SANDBOX;
    }

    public function isLoggingActive(): bool
    {
        return (bool) $this->getSettingValue(self::LOGGING_ACTIVE);
    }

    public function getAPIKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_API_KEY : self::LIVE_API_KEY);
        return (string)$this->getSettingValue($key);
    }

    public function getClientKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_CLIENT_KEY : self::LIVE_CLIENT_KEY);
        return (string)$this->getSettingValue($key);
    }

    public function getHmacSignature(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_HMAC_SIGNATURE : self::LIVE_HMAC_SIGNATURE);
        return (string)$this->getSettingValue($key);
    }

    public function getMerchantAccount(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_MERCHANT_ACCOUNT : self::LIVE_MERCHANT_ACCOUNT);
        return (string)$this->getSettingValue($key);
    }

    public function getNotificationUsername(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_USERNAME : self::LIVE_NOTIFICATION_USERNAME);
        return (string)$this->getSettingValue($key);
    }

    public function getNotificationPassword(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_PASSWORD : self::LIVE_NOTIFICATION_PASSWORD);
        return (string)$this->getSettingValue($key);
    }

    public function isSeperateCapture(string $paymentId): bool
    {
        return (bool)$this->getSettingValue('osc_adyen_SeperateCapture_' . $paymentId);
    }

    public function getLocaleForCountryIso(string $countryIso): string
    {
        // make sure provided keys and searched keys are in lower case
        $countryIso = strtolower($countryIso);
        $languages = array_change_key_case($this->getSettingValue('osc_adyen_Languages'), CASE_LOWER);

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
