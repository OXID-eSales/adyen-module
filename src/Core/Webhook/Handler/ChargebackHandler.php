<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;

final class ChargebackHandler extends WebhookHandlerBase
{
    public const CHARGEBACK_EVENT_CODE = "CHARGEBACK";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event): void
    {
        /** @var AdyenOrder $order */
        $order = $this->order;
        $order->setAdyenOrderFolder('ORDERFOLDER_PROBLEMS');
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_CHARGEBACK;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_CHARGEBACK;
    }
}
