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
    public const SANDBOX_HMAC_SIGNATURE = 'osc_adyen_LiveHmacSignature';
    public const SANDBOX_MERCHANT_ACCOUNT = 'osc_adyen_LiveMerchantAccount';
    public const SANDBOX_NOTIFICATION_USERNAME = 'osc_adyen_LiveNotificationUsername';
    public const SANDBOX_NOTIFICATION_PASSWORD = 'osc_adyen_LiveNotificationPassword';

    public const LIVE_API_KEY = 'osc_adyen_LiveAPIKey';
    public const LIVE_CLIENT_KEY = 'osc_adyen_LiveClientKey';
    public const LIVE_HMAC_SIGNATURE = 'osc_adyen_LiveHmacSignature';
    public const LIVE_MERCHANT_ACCOUNT = 'osc_adyen_LiveMerchantAccount';
    public const LIVE_NOTIFICATION_USERNAME = 'osc_adyen_LiveNotificationUsername';
    public const LIVE_NOTIFICATION_PASSWORD = 'osc_adyen_LiveNotificationPassword';

    public const OPERATION_MODE_SANDBOX = 'sandbox';
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
            self::getAPIKey() &&
            self::getClientKey() &&
            self::getHmacSignature() &&
            self::getMerchantAccount() &&
            self::getNotificationUsername() &&
            self::getNotificationPassword()
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

    public function saveOperationMode(string $value): void
    {
        $this->saveSettingValue(self::OPERATION_MODE, $value);
    }

    public function isLoggingActive(): bool
    {
        return (bool) $this->getSettingValue(self::LOGGING_ACTIVE);
    }

    public function saveLoggingActive(bool $value): void
    {
        $this->saveSettingValue(self::LOGGING_ACTIVE, $value);
    }

    public function getAPIKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_API_KEY : self::LIVE_API_KEY);
        return (string)$this->getSettingValue($key);
    }

    public function saveAPIKey(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_API_KEY : self::LIVE_API_KEY);
        $this->saveSettingValue($key, $value);
    }

    public function getClientKey(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_CLIENT_KEY : self::LIVE_CLIENT_KEY);
        return (string)$this->getSettingValue($key);
    }

    public function saveClientKey(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_CLIENT_KEY : self::LIVE_CLIENT_KEY);
        $this->saveSettingValue($key, $value);
    }

    public function getHmacSignature(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_HMAC_SIGNATURE : self::LIVE_HMAC_SIGNATURE);
        return (string)$this->getSettingValue($key);
    }

    public function saveHmacSignature(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_HMAC_SIGNATURE : self::LIVE_HMAC_SIGNATURE);
        $this->saveSettingValue($key, $value);
    }

    public function getMerchantAccount(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_MERCHANT_ACCOUNT : self::LIVE_MERCHANT_ACCOUNT);
        return (string)$this->getSettingValue($key);
    }

    public function saveMerchantAccount(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_MERCHANT_ACCOUNT : self::LIVE_MERCHANT_ACCOUNT);
        $this->saveSettingValue($key, $value);
    }

    public function getNotificationUsername(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_USERNAME : self::LIVE_NOTIFICATION_USERNAME);
        return (string)$this->getSettingValue($key);
    }

    public function saveNotificationUsername(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_USERNAME : self::LIVE_NOTIFICATION_USERNAME);
        $this->saveSettingValue($key, $value);
    }

    public function getNotificationPassword(): string
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_PASSWORD : self::LIVE_NOTIFICATION_PASSWORD);
        return (string)$this->getSettingValue($key);
    }

    public function saveNotificationPassword(string $value): void
    {
        $key = ($this->isSandBoxMode() ? self::SANDBOX_NOTIFICATION_PASSWORD : self::LIVE_NOTIFICATION_PASSWORD);
        $this->saveSettingValue($key, $value);
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
