<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class RefundHandler extends WebhookHandlerBase
{
    public const REFUND_EVENT_CODE = "REFUND";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event): void
    {
        /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
        $order = $this->order;
        $order->setAdyenOrderStatus('OK');
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_REFUND;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_REFUNDED;
    }
}
