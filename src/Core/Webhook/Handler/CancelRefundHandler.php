<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class CancelRefundHandler extends WebhookHandlerBase
{
    public const CANCELORREFUND_EVENT_CODE = "CANCEL_OR_REFUND";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event): void
    {
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_CANCEL;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_CANCELLED;
    }
}
