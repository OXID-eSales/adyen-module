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
        $amountBefore = $this->collectAmountForAdyenRefund();

        parent::storno();

        $this->runAdyenRefund($amountBefore);
    }

    /**
     * @inheritDoc
     */
    public function deleteThisArticle(): void
    {
        $amountBefore = $this->collectAmountForAdyenRefund();

        parent::deleteThisArticle();

        $this->runAdyenRefund($amountBefore);
    }

    protected function collectAmountForAdyenRefund(): float
    {
        $order = oxNew(Order::class);
        $orderLoaded = $order->load($this->getEditObjectId());
        $amount = 0.0;

        if ($orderLoaded) {
            $amount = (float)$order->getTotalOrderSum();
        }
        return $amount;
    }

    protected function runAdyenRefund(float $amountBefore): void
    {
        $amountAfter = $this->collectAmountForAdyenRefund();
        $order = oxNew(Order::class);
        $orderLoaded = $order->load($this->getEditObjectId());
        $amount = $amountBefore - $amountAfter;
        /** @var \OxidSolutionCatalysts\Adyen\Model\Order $order */
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
