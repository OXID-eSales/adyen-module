<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\Order;

class CaptureHandler extends WebhookHandlerBase
{
    private const CAPTURE_EVENT_CODE = "CAPTURE";

    public function updateStatus(array $notificationItem): void
    {
        $eventCode = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_CODE];

        if ($eventCode != self::CAPTURE_EVENT_CODE) {
            Registry::getLogger()->debug("eventCode is not CAPTURE: ", $notificationItem);
        }

        $pspReference = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_PSP_REFERENCE];

        $price = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_AMOUNT]
            [self::JSON_FIELD_PRICE];

        $timestamp = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_DATE];

        $order = $this->getOrderByAdyenPSPReference($pspReference);
        if (is_null($order)) {
            Registry::getLogger()->debug("order not found by psp reference " . $pspReference);
            return;
        }

        $adyenHistoryParent = oxNew(AdyenHistory::class);
        $isLoaded = $adyenHistoryParent->loadByOxOrderId($order->getId());

        if (!$isLoaded) {
            Registry::getLogger()->debug("adyen history item not found by psp reference " . $pspReference);
            return;
        }

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId(Registry::getConfig()->getShopId());
        $adyenHistory->setPrice($price);
        $adyenHistory->setTimeStamp($timestamp);
        $adyenHistory->setPSPReference($pspReference . "_CAPTURE");
        $adyenHistory->setParentPSPReference($pspReference);
        $adyenHistory->setAdyenStatus($eventCode);
        $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_CAPTURE);

        $adyenHistory->save();
    }
}
