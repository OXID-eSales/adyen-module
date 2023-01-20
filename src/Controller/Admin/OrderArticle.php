<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\Admin\OrderArticle
 */
class OrderArticle extends OrderArticle_parent
{
    use ServiceContainer;

    /**
     * @inheritDoc
     */
    public function storno(): void
    {
        $order = oxNew(Order::class);
        $orderLoaded = $order->load($this->getEditObjectId());
        $amountBefore = 0;

        // collect the Order Amount before the change
        /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
        if ($orderLoaded && $order->isAdyenOrder()) {
            $amountBefore = (float)$order->getTotalOrderSum();
        }

        parent::storno();

        // load order again because of the changes
        $orderLoaded = $order->load($this->getEditObjectId());

        // if Refund is possible, collect the Order Amount after the change
        if ($orderLoaded && $order->isAdyenOrder() && $order->isAdyenRefundPossible()) {
            $amountAfter = (float)$order->getTotalOrderSum();
            $amount = $amountBefore - $amountAfter;
            $order->refundAdyenOrder($amount);
            // set transstatus again, because recalculate order set the transstatus back to "NOT_FINISHED"
            $order->setAdyenOrderStatus('OK');
        }
    }
}
