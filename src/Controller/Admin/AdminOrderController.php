<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Payment as eShopPayment;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Service\Payment as paymentService;

/**
 * Order class wrapper for Adyen module
 */
class AdminOrderController extends AdminDetailsController
{
    use ServiceContainer;

    protected ?Order $editObject = null;

    protected ?AdyenHistoryList $adyenHistoryList = null;

    protected ?bool $isCapturePossible = null;

    protected ?bool $isRefundPossible = null;

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
        $order = $this->getEditObject();
        return (
            $order &&
            $order->isAdyenOrder()
        );
    }
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function captureAdyenAmount(): void
    {
        /** @var Order $order */
        $order = $this->getEditObject();
        $pspReference = $order->getFieldData('adyenpspreference');
        $reference = $order->getFieldData('oxordernr');

        $request = Registry::getRequest();
        $amount = (float) $request->getRequestParameter('capture_amount');
        $currency = $request->getRequestParameter('capture_currency');

        $paymentService = $this->getServiceFromContainer(paymentService::class);
        $success = $paymentService->doAdyenCapture(
            $amount,
            $pspReference,
            $reference
        );

        if ($success) {
            $captureResult = $paymentService->getCaptureResult();

            // everything is fine, we can save the references
            if (isset($captureResult['paymentPspReference'])) {
                $pspReference = $captureResult['pspReference'];
                $parentPspReference = $captureResult['paymentPspReference'];

                $adyenHistory = oxNew(AdyenHistory::class);
                $adyenHistory->setPSPReference($pspReference);
                $adyenHistory->setParentPSPReference($parentPspReference);
                $adyenHistory->setOrderId($order->getId());
                $adyenHistory->setPrice($amount);
                $adyenHistory->setCurrency($currency);
                if (isset($captureResult['status'])) {
                    $adyenHistory->setAdyenStatus($captureResult['status']);
                }
                $adyenHistory->save();
            }
        }
    }

    public function isAdyenCapturePossible(): bool
    {
        if (is_null($this->isCapturePossible)) {
            $this->isCapturePossible = false;
            if ($this->isAdyenOrder()) {
                /** @var Order $order */
                $order = $this->getEditObject();
                /** @var Payment $payment */
                $payment = oxNew(eShopPayment::class);
                $payment->load($order->getFieldData('oxpaymenttype'));
                $this->isCapturePossible = (
                    $payment->isAdyenSeperateCapture() &&
                    !$order->isAdyenOrderPaid()
                );
            }
        }
        return $this->isCapturePossible;
    }

    public function isAdyenRefundPossible(): bool
    {
        if (is_null($this->isRefundPossible)) {
            $this->isRefundPossible = false;
            if ($this->isAdyenOrder()) {
                /** @var Order $order */
                $order = $this->getEditObject();
                /** @var Payment $payment */
                $payment = oxNew(eShopPayment::class);
                $payment->load($order->getFieldData('oxpaymenttype'));
                $this->isRefundPossible = (
                    $order->isAdyenOrderPaid()
                );
            }
        }
        return $this->isRefundPossible;
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

    /**
     * Returns Adyen-History
     *
     * @return AdyenHistoryList
     */
    public function getHistoryList(): ?AdyenHistoryList
    {
        $oxId = $this->getEditObjectId();
        if (is_null($this->adyenHistoryList)) {
            $adyenHistoryList = oxNew(AdyenHistoryList::class);
            $adyenHistoryList->getAdyenHistoryList($oxId);
            if ($adyenHistoryList->count()) {
                $this->adyenHistoryList = $adyenHistoryList;
            }
        }
        return $this->adyenHistoryList;
    }
}
