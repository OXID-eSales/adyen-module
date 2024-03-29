<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class CancellationHandler extends WebhookHandlerBase
{
    public const CANCEL_EVENT_CODE = "CANCELLATION";

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
