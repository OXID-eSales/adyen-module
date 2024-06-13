<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook\Handler;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;
use Psr\Log\LoggerInterface;

final class RefundHandler extends WebhookHandlerBase
{
    public const REFUND_EVENT_CODE = "REFUND";

    /**
     * @param Event $event
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function additionalUpdates(Event $event): void
    {
        /** @var AdyenOrder $order */
        $order = $this->order;
        $order->setAdyenOrderStatus('OK');
    }

    protected function getAdyenAction(): string
    {
        return Module::ADYEN_ACTION_REFUND;
    }

    protected function getAdyenStatus(): string
    {
        return Module::ADYEN_STATUS_REFUNDED;
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

        if (!$event->isSuccess()) {
            try {
                $this->setData($event);
                $this->updateStatusFailed($event);
                return;
            } catch (WebhookEventTypeException | Exception $e) {
                $this->getLogger()->debug($e->getMessage());
            }
        }
        parent::handle($event);
    }
    /**
     * make functions mockable which uses the logger
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getLogger(): LoggerInterface
    {
        return Registry::getLogger();
    }
    public function updateStatusFailed(Event $event): void
    {
        $this->setHistoryEntry(
            $this->order->getId(),
            $this->shopId,
            $event->getAmountValue(),
            $event->getAmountCurrency(),
            $event->getEventDate(),
            $this->pspReference,
            $this->parentPspReference,
            MODULE::ADYEN_STATUS_REFUNDFAILED,
            $this->getAdyenAction()
        );
    }
}
