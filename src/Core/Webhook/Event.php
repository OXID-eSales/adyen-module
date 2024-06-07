<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use Adyen\AdyenException;
use Adyen\Util\HmacSignature;
use DateTime;
use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Config;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\AdyenPayment;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

final class Event
{
    use AdyenPayment;
    use ServiceContainer;

    private array $rawData;
    private array $item;
    private string $eventType;
    private bool $isLive = false;
    private bool $isSuccess = false;
    private bool $isHMACVerified = true;
    private bool $isMerchantVerified = false;
    private string $eventDate;
    private string $pspReference;
    private string $merchantAccountCode;
    private string $merchantReference;
    private string $originalReference;
    private float $amountValue;
    private string $amountCurrency;

    private HmacSignature $hmacSignatureUtil;

    /**
     * Event constructor.
     *
     * @param array $rawData
     * @throws Exception
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
        $this->initData();
        $this->verifyHMACSignature();
        $this->verifyMerchantAccountCode();
    }

    public function getItem(): array
    {
        return $this->item;
    }

    public function isLive(): bool
    {
        return $this->isLive;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function isHMACVerified(): bool
    {
        //return true;
        return $this->isHMACVerified;
    }

    public function isMerchantVerified(): bool
    {
        return $this->isMerchantVerified;
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getPspReference(): string
    {
        return $this->pspReference;
    }

    public function getParentPspReference(): string
    {
        return $this->originalReference;
    }

    public function getMerchantAccount(): string
    {
        return $this->merchantAccountCode;
    }

    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }

    public function getAmountValue(): float
    {
        return $this->amountValue;
    }

    public function getAmountCurrency(): string
    {
        return $this->amountCurrency;
    }

    /**
     * Analyze the RawData and push it to the getter-Vars
     * Important: JSON and HTTP POST notifications always contain a single NotificationRequestItem object
     * @return void
     * @throws Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function initData(): void
    {
        $this->isLive = isset($this->rawData['live']) && $this->rawData['live'] === 'true';

        $this->item = $this->rawData['notificationItems'][0]['NotificationRequestItem'] ?? [];
        $this->eventType = $this->item['eventCode'] ?? '';
        $this->isSuccess = isset($this->item['success']) && $this->item['success'] === 'true';
        $this->pspReference = $this->item['pspReference'] ?? '';
        $this->merchantReference = $this->item['merchantReference'] ?? '';
        $this->merchantAccountCode = $this->item['merchantAccountCode'] ?? '';
        $this->originalReference = $this->item['originalReference'] ?? '';
        $this->amountCurrency = $this->item['amount']['currency'] ?? '';

        // timestamp
        $rawEventDate = new DateTime($this->item['eventDate'] ?? '');
        $this->eventDate = $rawEventDate->format('Y-m-d H:i:s');

        // amountValue
        /** @var int $rawAmountValue */
        $rawAmountValue = $this->item['amount']['value'] ?? 0;
        /** @var Config $config */
        $config = Registry::getConfig();
        $currencyObj = $config->getCurrencyObject($this->amountCurrency);
        /** @var null|string $currencyDecimals */
        $currencyDecimals = $currencyObj->decimal ?? '';
        $this->amountValue = $this->getOxidAmount($rawAmountValue, (int)$currencyDecimals);

        // whether getting HmacSignature mock from unit test or new HmacSignature object for production
        $this->hmacSignatureUtil = $this->rawData['hmacSignatureUtil'] ?? new HmacSignature();
    }

    protected function verifyHMACSignature(): void
    {
        /** @var ModuleSettings $moduleSettings */
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $hmacKey = $moduleSettings->getHmacSignature();

        // verify the Signature if we have one
        if (!$hmacKey) {
            return;
        }

        try {
            $this->isHMACVerified = $this->hmacSignatureUtil->isValidNotificationHMAC(
                $hmacKey,
                $this->item
            );
        } catch (AdyenException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }

    protected function verifyMerchantAccountCode(): void
    {
        /** @var ModuleSettings $moduleSettings */
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $this->isMerchantVerified = $this->merchantAccountCode === $moduleSettings->getMerchantAccount();
    }
}
