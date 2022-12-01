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
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidEsales\Eshop\Application\Model\Order;

final class AuthorizationHandler extends WebhookHandlerBase
{
    public const AUTHORIZATION_EVENT_CODE = "AUTHORIZATION";

    /**
     * @param Event $event
     * @return void
     * @throws WebhookEventTypeException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function additionalUpdates(Event $event, Order $order): void
    {
        /** @var null|string $paymentId */
        $paymentId = $order->getFieldData('oxpaymenttype');
        if (is_null($paymentId)) {
            return;
        }
        /** @var \OxidSolutionCatalysts\Adyen\Model\Payment $payment */
        $payment = oxNew(Payment::class);
        $payment->load($paymentId);
        if ($payment->isAdyenImmediateCapture()) {
            /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
            $order->markAdyenOrderAsPaid();
        }
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_STATUS_AUTHORISED;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_ACTION_AUTHORIZE;
    }
}
