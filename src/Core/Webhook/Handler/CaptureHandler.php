<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class CaptureHandler extends WebhookHandlerBase
{
    public const CAPTURE_EVENT_CODE = "CAPTURE";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event): void
    {
        /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
        $order = $this->order;
        $order->markAdyenOrderAsPaid();
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_CAPTURE;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_CAPTURED;
    }
}
