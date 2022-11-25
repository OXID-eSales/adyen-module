<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;

final class CaptureHandler extends WebhookHandlerBase
{
    private const CAPTURE_EVENT_CODE = "CAPTURE";

    /**
     * @param array $notificationItem
     * @return void
     * @throws WebhookEventTypeException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function updateStatus(array $notificationItem): void
    {
        $eventCode = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_CODE];

        if ($eventCode != self::CAPTURE_EVENT_CODE) {
            throw WebhookEventTypeException::handlerNotFound(self::CAPTURE_EVENT_CODE);
        }

        $pspReference = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_PSP_REFERENCE];

        $parentPspReference = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_PARENT_PSP_REFERENCE];

        $price = (float)$notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_AMOUNT]
            [self::JSON_FIELD_PRICE];

        // TODO: Convert Price correct
        $price /= 100;

        $timestamp = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_DATE];

        $order = $this->getOrderByAdyenPSPReference($pspReference);
        if (is_null($order)) {
            Registry::getLogger()->debug("order not found by psp reference " . $pspReference);
            return;
        }

        $order->markAdyenOrderAsPaid();

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
        $adyenHistory->setPSPReference($pspReference);
        $adyenHistory->setParentPSPReference($parentPspReference);
        // TODO: Translate Adyen status
        $eventCode = strtolower($eventCode);
        if ($eventCode === 'capture') {
            $eventCode = Module::ADYEN_STATUS_CAPTURED;
        }
        $adyenHistory->setAdyenStatus($eventCode);
        $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_CAPTURE);

        $adyenHistory->save();
    }
}
