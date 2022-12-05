<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

abstract class WebhookHandlerBase
{
    use ServiceContainer;

    public function handle(Event $event): void
    {
        if (!$event->isHMACVerified()) {
            Registry::getLogger()->debug("Webhook: HMAC could not verified");
            return;
        }

        if (!$event->isMerchantVerified()) {
            Registry::getLogger()->debug("Webhook: MerchantCode could not verified");
            return;
        }

        if ($event->isSuccess()) {
            try {
                $this->updateStatus($event);
            } catch (WebhookEventTypeException $e) {
                Registry::getLogger()->debug($e->getMessage());
            }
        }
    }

    protected function getOrderByAdyenPSPReference(string $pspReference): ?Order
    {
        $result = null;
        $adyenHistoryList = oxNew(AdyenHistoryList::class);

        $oxidOrderId = $adyenHistoryList->getOxidOrderIdByPSPReference($pspReference);

        $order = oxNew(Order::class);
        if ($order->load($oxidOrderId)) {
            $result = $order;
        }
        return $result;
    }

    /**
     * @param Event $event
     * @return void
     * @throws WebhookEventTypeException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function updateStatus(Event $event): void
    {
        /** @var Context $context */
        $context = $this->getServiceFromContainer(Context::class);

        $pspReference = $event->getPspReference();
        $parentPspReference = $event->getParentPspReference() !== '' ?
            $event->getParentPspReference() :
            $pspReference;
        $order = $this->getOrderByAdyenPSPReference($pspReference);
        if (is_null($order)) {
            Registry::getLogger()->debug("order not found by psp reference " . $pspReference);
            return;
        }

        $payment = oxNew(Payment::class);

        /** @var null|string $paymentId */
        $paymentId = $order->getFieldData('oxpaymenttype');
        if (!is_null($paymentId)) {
            $payment->load($paymentId);
        }

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId($context->getCurrentShopId());
        $adyenHistory->setPrice($event->getAmountValue());
        $adyenHistory->setCurrency($event->getAmountCurrency());
        $adyenHistory->setTimeStamp($event->getEventDate());
        $adyenHistory->setPSPReference($pspReference);
        $adyenHistory->setParentPSPReference($parentPspReference);
        $adyenHistory->setAdyenStatus($this->getAdyenStatus($event, $order, $payment));
        $adyenHistory->setAdyenAction($this->getAdyenAction($event, $order, $payment));

        $adyenHistory->save();

        $this->additionalUpdates($event, $order, $payment);
    }

    /**
     * @param Event $event
     * @param Order $order
     * @param Payment $payment
     * @return void
     * @throws WebhookEventTypeException
     */
    abstract protected function additionalUpdates(Event $event, Order $order, Payment $payment): void;

    abstract protected function getAdyenStatus(Event $event, Order $order, Payment $payment): string;

    abstract protected function getAdyenAction(Event $event, Order $order, Payment $payment): string;
}
