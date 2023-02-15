<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Model\Order as EshopModelOrder;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use Psr\Log\LoggerInterface;

abstract class WebhookHandlerBase
{
    use ServiceContainer;

    protected int $shopId;
    protected string $pspReference;
    protected string $parentPspReference;
    protected EshopModelOrder $order;
    protected Payment $payment;
    protected AdyenHistoryList $adyenHistoryList;
    protected Context $context;

    public function __construct(
        ?Payment $payment = null,
        ?EshopModelOrder $order = null,
        ?AdyenHistoryList $adyenHistoryList = null,
        ?Context $context = null
    ) {
        // whether getting mock objects from unit test or new objects for production
        $this->payment = $payment ?? oxNew(Payment::class);
        $this->order = $order ?? oxNew(EshopModelOrder::class);
        $this->adyenHistoryList = $adyenHistoryList ?? oxNew(AdyenHistoryList::class);
        $this->context = $context ?? $this->getServiceFromContainer(Context::class);
    }

    public function handle(Event $event): void
    {
        if (!$event->isHMACVerified()) {
            $this->getLogger()->debug("Webhook: HMAC could not verified");
            return;
        }

        if (!$event->isMerchantVerified()) {
            $this->getLogger()->debug("Webhook: MerchantCode could not verified");
            return;
        }

        if ($event->isSuccess()) {
            try {
                $this->setData($event);
                $this->updateStatus($event);
            } catch (WebhookEventTypeException | Exception $e) {
                $this->getLogger()->debug($e->getMessage());
            }
        }
    }

    protected function getOrderByAdyenPSPReference(string $pspReference): ?EshopModelOrder
    {
        $result = null;

        $oxidOrderId = $this->adyenHistoryList->getOxidOrderIdByPSPReference($pspReference);

        if ($this->order->load($oxidOrderId)) {
            $result = $this->order;
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
        $this->shopId = $this->context->getCurrentShopId();

        $this->pspReference = $event->getPspReference();
        $this->parentPspReference = $event->getParentPspReference() !== '' ?
            $event->getParentPspReference() :
            $this->pspReference;
        /** @var Order $order */
        $order = $this->getOrderByAdyenPSPReference($this->pspReference);
        if (!is_object($order)) {
            throw new Exception("order not found by psp reference " . $this->pspReference);
        }
        $this->order = $order;

        /** @var null|string $paymentId */
        $paymentId = $this->order->getAdyenStringData('oxpaymenttype');
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
            $this->getLogger()->info($e->getMessage());
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    abstract protected function additionalUpdates(Event $event): void;

    abstract protected function getAdyenStatus(): string;

    abstract protected function getAdyenAction(): string;

    private function getLogger(): LoggerInterface
    {
        return Registry::getLogger();
    }
}
