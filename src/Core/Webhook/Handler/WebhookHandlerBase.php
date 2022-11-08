<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use Adyen\AdyenException;
use Adyen\Util\HmacSignature;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class WebhookHandlerBase
{
    protected const JSON_FIELD_LIVE = "live";
    protected const JSON_FIELD_NOTIFICATION_ITEMS = "notificationItems";
    protected const JSON_FIELD_NOTIFICATION_REQUEST_ITEM = "NotificationRequestItem";
    protected const JSON_FIELD_ADDITIONAL_DATA = "additionalData";
    protected const JSON_FIELD_HMAC_SIGNATURE = "hmacSignature";
    protected const JSON_FIELD_EVENT_CODE = "eventCode";
    protected const JSON_FIELD_SUCCESS = "success";
    protected const JSON_FIELD_EVENT_DATE = "eventDate";
    protected const JSON_FIELD_MERCHANT_ACCOUNT_CODE = "merchantAccountCode";
    protected const JSON_FIELD_MERCHANT_REFERENCE = "merchantReference";
    protected const JSON_FIELD_PSP_REFERENCE = "pspReference";
    protected const JSON_FIELD_AMOUNT = "amount";
    protected const JSON_FIELD_PRICE = "value";
    protected const JSON_FIELD_CURRENCY = "currency";

    public function handle(Event $event): void
    {
        if (!$this->verifyHMACSignature($event)) {
            return;
        }

        if (!$this->verifyMerchantAccountCode($event)) {
            return;
        }

        foreach ($this->getNotificationItems($event) as $notificationItem) {
            $success = $notificationItem
                [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
                [self::JSON_FIELD_SUCCESS];

            if ($success) {
                $this->updateStatus($notificationItem);
            }
        }
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function isLiveStatus(Event $event): bool
    {
        return $event->getData()[self::JSON_FIELD_LIVE] == "true";
    }

    /**
     * @param Event $event
     * @return array
     */
    public function getNotificationItems(Event $event): array
    {
        return $event->getData()[self::JSON_FIELD_NOTIFICATION_ITEMS];
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function verifyHMACSignature(Event $event): bool
    {
        try {
            /** @var ModuleSettings $moduleSettings */
            $moduleSettings = ContainerFactory::getInstance()
                ->getContainer()
                ->get(ModuleSettings::class);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            return false;
        }

        $hmacKey = $moduleSettings->getHmacSignature();
        $hmac = new HmacSignature();

        try {
            foreach ($this->getNotificationItems($event) as $notificationItem) {
                $params = $notificationItem[self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM];
                if (!$hmac->isValidNotificationHMAC($hmacKey, $params)) {
                    return false;
                }
            }
        } catch (AdyenException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            return false;
        }

        return true;
    }

    public function verifyMerchantAccountCode(Event $event): bool
    {
        try {
            /** @var ModuleSettings $moduleSettings */
            $moduleSettings = ContainerFactory::getInstance()
                ->getContainer()
                ->get(ModuleSettings::class);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            return false;
        }

        $merchantAccount = $moduleSettings->getMerchantAccount();

        foreach ($this->getNotificationItems($event) as $notificationItem) {
            $testMerchantAccount = $notificationItem
                [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
                [self::JSON_FIELD_MERCHANT_ACCOUNT_CODE];

            if ($merchantAccount != $testMerchantAccount) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param string $pspReference
     * @return Order|null
     */
    public function getOrderByAdyenPSPReference(string $pspReference): ?Order
    {
        $adyenHistoryList = oxNew(AdyenHistoryList::class);
        $adyenHistoryList->init(AdyenHistory::class);
        $oxidOrderId = $adyenHistoryList->getOxidOrderIdByPSPReference($pspReference);

        if (is_null($oxidOrderId)) {
            return null;
        }

        $order = oxNew(Order::class);
        $order->load($oxidOrderId);

        return $order;
    }

    abstract public function updateStatus(array $notificationItem): void;
}
