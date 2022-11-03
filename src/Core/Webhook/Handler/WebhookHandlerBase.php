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
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class WebhookHandlerBase
{
    protected const LIVE_JSON_FIELD = "live";
    protected const NOTIFICATION_ITEMS_JSON_FIELD = "notificationItems";
    protected const NOTIFICATION_REQUEST_ITEM_JSON_FIELD = "NotificationRequestItem";
    protected const ADDITIONAL_DATA_JSON_FIELD = "additionalData";
    protected const HMAC_SIGNATURE_JSON_FIELD = "hmacSignature";
    protected const EVENT_CODE_JSON_FIELD = "eventCode";
    protected const SUCCESS_JSON_FIELD = "success";
    protected const EVENT_DATE_JSON_FIELD = "eventDate";
    protected const MERCHANT_ACCOUNT_CODE_JSON_FIELD = "merchantAccountCode";
    protected const MERCHANT_REFERENCE_JSON_FIELD = "merchantReference";
    protected const PSP_REFERENCE_JSON_FIELD = "pspReference";

    public function handle(Event $event): void
    {
        if (!$this->verifyHMACSignature($event)) {
            return;
        }

        foreach ($this->getNotificationItems($event) as $notificationItem) {
            $pspReference = $notificationItem
                [self::NOTIFICATION_REQUEST_ITEM_JSON_FIELD]
                [self::PSP_REFERENCE_JSON_FIELD];

            if ($pspReference) {
                $order = $this->getOrderByAdyenPSPReference($pspReference);

                $success = $notificationItem
                    [self::NOTIFICATION_REQUEST_ITEM_JSON_FIELD]
                    [self::SUCCESS_JSON_FIELD];

                if ($success) {
                    $eventCode = $notificationItem
                        [self::NOTIFICATION_REQUEST_ITEM_JSON_FIELD]
                        [self::EVENT_CODE_JSON_FIELD];

                    $this->updateStatus($order, $eventCode);
                }
            }
        }
    }

    /**
     * @param Event $event
     */
    public function isLiveStatus(Event $event): bool
    {
        return $event->getData()[self::LIVE_JSON_FIELD] == "true";
    }

    /**
     * @param Event $event
     */
    public function getNotificationItems(Event $event): array
    {
        return $event->getData()[self::NOTIFICATION_ITEMS_JSON_FIELD];
    }

    /**
     * @param Event $event
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
                $params = $notificationItem[self::NOTIFICATION_REQUEST_ITEM_JSON_FIELD];
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

    /**
     * @param string $pspReference
     */
    public function getOrderByAdyenPSPReference(string $pspReference): Order
    {
        $adyenHistoryList = oxNew(AdyenHistoryList::class);
        $oxidOrderId = $adyenHistoryList->getOxidOrderIdByPSPReference($pspReference);

        $order = oxNew(Order::class);
        $order->load($oxidOrderId);

        return $order;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function updateStatus(Order $order, string $eventCode): void
    {
        //TODO: Update status in oxorder table and any tables...
        //TODO: remove SuppressWarnings when you´ve done develop the interaction with $order and $eventCode
    }
}
