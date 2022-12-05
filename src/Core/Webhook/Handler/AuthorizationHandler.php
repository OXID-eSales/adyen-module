<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidEsales\Eshop\Application\Model\Order;

final class AuthorizationHandler extends WebhookHandlerBase
{
    /**
     * "AUTHORISATION" in british english spelling
     */
    public const AUTHORIZATION_EVENT_CODE = "AUTHORISATION";

    /**
     * @param Event $event
     * @param Order $order
     * @param Payment $payment
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function additionalUpdates(Event $event, Order $order, Payment $payment): void
    {
        /** @var null|\OxidSolutionCatalysts\Adyen\Model\Payment $payment */
        if (!is_null($payment) && $payment->isAdyenImmediateCapture()) {
            /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
            $order->markAdyenOrderAsPaid();
        }
    }

    protected function getAdyenAction(Event $event, Order $order, Payment $payment): string
    {
        /** @var null|\OxidSolutionCatalysts\Adyen\Model\Payment $payment */
        return (!is_null($payment) && $payment->isAdyenImmediateCapture()) ?
            Module::ADYEN_STATUS_CAPTURED :
            Module::ADYEN_STATUS_AUTHORISED;
    }

    protected function getAdyenStatus(Event $event, Order $order, Payment $payment): string
    {
        /** @var null|\OxidSolutionCatalysts\Adyen\Model\Payment $payment */
        return (!is_null($payment) && $payment->isAdyenImmediateCapture()) ?
            Module::ADYEN_ACTION_CAPTURE :
            Module::ADYEN_ACTION_AUTHORIZE;
    }
}
