<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Model\Payment as AdyenPayment;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;

final class AuthorisationHandler extends WebhookHandlerBase
{
    public const AUTHORISATION_EVENT_CODE = "AUTHORISATION";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function additionalUpdates(Event $event): void
    {
        /** @var null|AdyenPayment $payment */
        $payment = $this->payment;
        if ($payment->isAdyenImmediateCapture()) {
            /** @var AdyenOrder $order */
            $order = $this->order;
            $order->markAdyenOrderAsPaid();

            $this->setHistoryEntry(
                $order->getId(),
                $this->shopId,
                $event->getAmountValue(),
                $event->getAmountCurrency(),
                $event->getEventDate(),
                $this->pspReference,
                $this->parentPspReference,
                Module::ADYEN_STATUS_CAPTURED,
                Module::ADYEN_ACTION_CAPTURE
            );
        }
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
