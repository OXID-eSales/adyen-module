<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;

final class AuthorisationAdjustmentHandler extends WebhookHandlerBase
{
    public const AUTHORISATION_ADJUSTMENT_EVENT_CODE = "AUTHORISATION_ADJUSTMENT";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function additionalUpdates(Event $event): void
    {
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_AUTHORIZE;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_AUTHORISED;
    }
}
