<?php

namespace OxidSolutionCatalysts\Adyen\Service\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;

class OrderArticleControllerService
{
    public function refundOrderIfNeeded(
        bool $orderLoaded,
        float $amount,
        Order $order
    ): void {
        /** @var AdyenOrder $order */
        if (
            $orderLoaded &&
            $amount > 0 &&
            $order->isAdyenOrder() &&
            $order->isAdyenRefundPossible()
        ) {
            $order->refundAdyenOrder($amount);
        }
    }
}
