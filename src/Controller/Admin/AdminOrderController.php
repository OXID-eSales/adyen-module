<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * Order class wrapper for Adyen module
 */
class AdminOrderController extends AdminDetailsController
{
    use ServiceContainer;

    protected ?Order $editObject = null;
    protected ?AdyenHistoryList $adyenHistoryList = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = '@' . \OxidSolutionCatalysts\Adyen\Core\Module::MODULE_ID . '/osc_adyen_order'; //NOSONAR

    /**
     * Executes parent method parent::render()
     * name of template file "osc_adyen_order.tpl".
     *
     * @return string
     */
    public function render(): string
    {
        parent::render();

        $oxId = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $oxId;
        if ($oxId) {
            $this->_aViewData["edit"] = $this->getEditObject();
            $this->_aViewData["history"] = $this->getHistoryList();
        }
        return $this->_sThisTemplate;
    }

    /**
     * Method checks is this a AdyenOrder
     *
     * @return bool
     */
    public function isAdyenOrder(): bool
    {
        /** @var AdyenOrder $order */
        $order = $this->getEditObject();
        return $order->isAdyenOrder();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function captureAdyenAmount(): void
    {
        $request = Registry::getRequest();
        /** @var null|string $amount */
        $amount = $request->getRequestParameter('capture_amount');
        $amount = (float)($amount ?? '');

        $order = $this->getEditObject();
        /** @var AdyenOrder $order */
        $order->captureAdyenOrder($amount);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function refundAdyenAmount(): void
    {
        $request = Registry::getRequest();
        /** @var null|string $amount */
        $amount = $request->getRequestParameter('refund_amount');
        $amount = (float)($amount ?? '');

        $order = $this->getEditObject();
        /** @var AdyenOrder $order */
        $order->refundAdyenOrder($amount);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function cancelAdyenOrder(): void
    {
        /** @var Order $order */
        $order = $this->getEditObject();
        $order->cancelOrder();
    }

    /**
     * Returns editable order object
     *
     * @return Order|null
     */
    public function getEditObject(): ?Order
    {
        $oxid = $this->getEditObjectId();
        if ($this->editObject === null && $oxid !== '-1') {
            $this->editObject = $this->getServiceFromContainer(OxNewService::class)->oxNew(Order::class);
            $this->editObject->load($oxid);
        }
        return $this->editObject;
    }

    /**
     * Returns Adyen-History
     *
     * @return AdyenHistoryList
     */
    public function getHistoryList(): ?AdyenHistoryList
    {
        $oxId = $this->getEditObjectId();
        if (is_null($this->adyenHistoryList)) {
            $adyenHistoryList = $this->getServiceFromContainer(OxNewService::class)->oxNew(AdyenHistoryList::class);
            $adyenHistoryList->getAdyenHistoryList($oxId);
            if ($adyenHistoryList->count()) {
                $this->adyenHistoryList = $adyenHistoryList;
            }
        }
        return $this->adyenHistoryList;
    }
}
