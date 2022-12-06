<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use Exception;
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

    protected int $shopId;
    protected string $pspReference;
    protected string $parentPspReference;
    protected Order $order;
    protected ?Payment $payment;

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
                $this->setData($event);
                $this->updateStatus($event);
            } catch (WebhookEventTypeException | Exception $e) {
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws Exception
     */
    public function setData(Event $event): void
    {
        /** @var Context $context */
        $context = $this->getServiceFromContainer(Context::class);
        $this->shopId = $context->getCurrentShopId();

        $this->pspReference = $event->getPspReference();
        $this->parentPspReference = $event->getParentPspReference() !== '' ?
            $event->getParentPspReference() :
            $this->pspReference;
        $order = $this->getOrderByAdyenPSPReference($this->pspReference);
        if (is_null($order)) {
            throw new Exception("order not found by psp reference " . $this->pspReference);
        }
        $this->order = $order;

        $this->payment = oxNew(Payment::class);

        /** @var null|string $paymentId */
        $paymentId = $this->order->getFieldData('oxpaymenttype');
        if (!is_null($paymentId)) {
            $this->payment->load($paymentId);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function updateStatus(Event $event): void
    {
        $this->setHistoryEntry(
            $this->order->getId(),
            $this->shopId,
            $event->getAmountValue(),
            $event->getAmountCurrency(),
            $event->getEventDate(),
            $this->pspReference,
            $this->parentPspReference,
            $this->getAdyenStatus(),
            $this->getAdyenAction()
        );

        $this->additionalUpdates($event);
    }

    protected function setHistoryEntry(
        string $orderId,
        int $shopId,
        float $amount,
        string $currency,
        string $timestamp,
        string $pspReference,
        string $parentPspReference,
        string $status,
        string $action
    ): void {
        try {
            $adyenHistory = oxNew(AdyenHistory::class);
            $adyenHistory->setOrderId($orderId);
            $adyenHistory->setShopId($shopId);
            $adyenHistory->setPrice($amount);
            $adyenHistory->setCurrency($currency);
            $adyenHistory->setTimeStamp($timestamp);
            $adyenHistory->setPSPReference($pspReference);
            $adyenHistory->setParentPSPReference($parentPspReference);
            $adyenHistory->setAdyenStatus($status);
            $adyenHistory->setAdyenAction($action);
            $adyenHistory->save();
        } catch (Exception $e) {
            Registry::getLogger()->info($e->getMessage());
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    abstract protected function additionalUpdates(Event $event): void;

    abstract protected function getAdyenStatus(): string;

    abstract protected function getAdyenAction(): string;
}
