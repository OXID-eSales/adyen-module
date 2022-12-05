<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class CaptureHandler extends WebhookHandlerBase
{
    public const CAPTURE_EVENT_CODE = "CAPTURE";

    /**
     * @param Event $event
     * @param Order $order
     * @param Payment $payment
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event, Order $order, Payment $payment): void
    {
        /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
        $order->markAdyenOrderAsPaid();
    }

    protected function getAdyenAction(Event $event, Order $order, Payment $payment): string
    {
        return Module::ADYEN_STATUS_CAPTURED;
    }

    protected function getAdyenStatus(Event $event, Order $order, Payment $payment): string
    {
        return Module::ADYEN_ACTION_CAPTURE;
    }
}
