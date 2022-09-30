<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * Order class wrapper for Adyen module
 */
class AdminOrderController extends AdminDetailsController
{
    use ServiceContainer;

    /**
     * Active order object
     * @var Order
     */
    protected $editObject = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'osc_adyen_order.tpl'; //NOSONAR

    /**
     * Executes parent method parent::render()
     * name of template file "osc_adyen_order.tpl".
     *
     * @return string
     */
    public function render(): string
    {
        parent::render();

        $oxid = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $oxid;
        if ($oxid) {
            $order = oxNew(Order::class);
            $order->load($oxid);

            $this->_aViewData["edit"] = $order;
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
        $order = $this->getEditObject();
        return (
            $order &&
            $order->isAdyenOrder()
        );
    }

    /**
     * Returns editable order object
     *
     * @return Order|null
     */
    public function getEditObject(): ?Order
    {
        $oxid = $this->getEditObjectId();
        if ($this->editObject === null && $oxid != '-1') {
            $this->editObject = oxNew(Order::class);
            $this->editObject->load($oxid);
        }

        return $this->editObject;
    }
}
