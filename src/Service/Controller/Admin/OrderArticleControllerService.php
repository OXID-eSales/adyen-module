<?php

namespace OxidSolutionCatalysts\Adyen\Service\Controller\Admin;

use OxidSolutionCatalysts\Adyen\Model\Order;

class OrderArticleControllerService
{
    public function refundOrderIfNeeded(
        bool $orderLoaded,
        float $amount,
        Order $order
    ): void {
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
