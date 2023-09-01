<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Controller\Admin\OrderArticleControllerService;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Traits\ParentMethodStubableTrait;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\Admin\OrderArticle
 */
class OrderArticle extends OrderArticle_parent
{
    use ServiceContainer;
    use ParentMethodStubableTrait;

    /**
     * @inheritDoc
     */
    public function storno()
    {
        $amountBefore = $this->collectAmountForAdyenRefund();

        $this->parentCall('storno');

        $this->runAdyenRefund($amountBefore);
    }

    /**
     * @inheritDoc
     */
    public function deleteThisArticle()
    {
        $amountBefore = $this->collectAmountForAdyenRefund();

        $this->parentCall('deleteThisArticle');

        $this->runAdyenRefund($amountBefore);
    }

    protected function collectAmountForAdyenRefund(): float
    {
        $order = $this->getServiceFromContainer(OxNewService::class)->oxNew(Order::class);
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
        $order = $this->getServiceFromContainer(OxNewService::class)->oxNew(Order::class);
        $orderLoaded = $order->load($this->getEditObjectId());
        $amount = $amountBefore - $amountAfter;
        /** @var AdyenOrder $order */
        $this->getServiceFromContainer(OrderArticleControllerService::class)->refundOrderIfNeeded(
            $orderLoaded,
            $amount,
            $order
        );
    }
}
