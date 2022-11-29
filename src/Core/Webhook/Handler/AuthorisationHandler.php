<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\Order;

final class AuthorisationHandler extends WebhookHandlerBase
{
    private const AUTHORIZATION_EVENT_CODE = "AUTHORISATION";

    /**
     * @param array $notificationItem
     * @return void
     * @throws WebhookEventTypeException
     */
    public function updateStatus(array $notificationItem): void
    {
        $eventCode = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_CODE];

        if ($eventCode != self::AUTHORIZATION_EVENT_CODE) {
            throw WebhookEventTypeException::handlerNotFound(self::AUTHORIZATION_EVENT_CODE);
        }

        $pspReference = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_PSP_REFERENCE];

        $price = (float)$notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_AMOUNT]
            [self::JSON_FIELD_PRICE];

        // TODO: Convert Price correct
        $price /= 100;

        $currency = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_AMOUNT]
            [self::JSON_FIELD_CURRENCY];

        $timestamp = $notificationItem
            [self::JSON_FIELD_NOTIFICATION_REQUEST_ITEM]
            [self::JSON_FIELD_EVENT_DATE];

        $order = $this->getOrderByAdyenPSPReference($pspReference);
        if (is_null($order)) {
            Registry::getLogger()->debug("order not found by psp reference " . $pspReference);
            return;
        }

        $paymentId = $order->getFieldData('oxpaymenttype');

        /** @var \OxidSolutionCatalysts\Adyen\Model\Payment $payment */
        $payment = oxNew(Payment::class);
        $payment->load($paymentId);
        if ($payment->isAdyenImmediateCapture()) {
            $order->markAdyenOrderAsPaid();
        }

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId(Registry::getConfig()->getShopId());
        $adyenHistory->setPrice($price);
        $adyenHistory->setCurrency($currency);
        $adyenHistory->setTimeStamp($timestamp);
        $adyenHistory->setPSPReference($pspReference);
        $adyenHistory->setParentPSPReference($pspReference);
        // TODO: Translate Adyen status
        $eventCode = strtolower($eventCode);
        if ($eventCode === 'authorisation') {
            $eventCode = Module::ADYEN_STATUS_AUTHORISED;
        }
        $adyenHistory->setAdyenStatus($eventCode);
        $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_AUTHORIZE);

        $adyenHistory->save();
    }
}
