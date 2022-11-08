<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\Order;

final class AuthorisationHandler extends WebhookHandlerBase
{
    private const AUTHORISATION_EVENT_CODE = "AUTHORISATION";

    /**
     * @param array $notificationRequestItem
     * @return void
     */
    public function updateStatus(array $notificationRequestItem): void
    {
        $eventCode = $notificationRequestItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_CODE];

        if ($eventCode != self::AUTHORISATION_EVENT_CODE) {
            Registry::getLogger()->debug("eventCode is not AUTHORISATION: ", $notificationRequestItem);
        }

        $pspReference = $notificationRequestItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_PSP_REFERENCE];

        $price = $notificationRequestItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_AMOUNT]
            [self::JSON_FIELD_PRICE];

        $timestamp = $notificationRequestItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_DATE];

        $order = oxNew(Order::class);
        $order->setAdyenPSPReference($pspReference);
        $order->save();

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId(Registry::getConfig()->getShopId());
        $adyenHistory->setOxPrice($price);
        $adyenHistory->setOxTimeStamp($timestamp);
        $adyenHistory->setPSPReference($pspReference);
        $adyenHistory->setAdyenStatus($eventCode);

        $adyenHistory->save();
    }
}
