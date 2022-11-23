<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Payment as eShopPayment;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\PaymentCapture;
use OxidSolutionCatalysts\Adyen\Service\PaymentRefund;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

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

    protected ?bool $isCancelPossible = null;
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

        $oxId = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $oxId;
        if ($oxId) {
            $this->_aViewData["edit"] = $this->getEditObject();
            $this->_aViewData["history"] = $this->getHistoryList();
            if ($this->isAdyenOrder()) {
                $this->_aViewData["adyenCaptureAmount"] = $this->getPossibleCaptureAmount();
                $this->_aViewData["adyenRefundAmount"] = $this->getPossibleRefundAmount();
            }
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
        $amount = (float)$request->getRequestParameter('capture_amount');
        $possibleAmount = $this->getPossibleCaptureAmount();
        $amount = min($amount, $possibleAmount);
        $currency = $request->getRequestParameter('capture_currency');

        $paymentService = $this->getServiceFromContainer(PaymentCapture::class);
        $success = $paymentService->doAdyenCapture(
            $amount,
            $pspReference,
            $reference
        );

        if ($success) {
            $captureResult = $paymentService->getCaptureResult();

            // everything is fine, we can save the references
            if (isset($captureResult['paymentPspReference'])) {
                $this->setAdyenHistoryEntry(
                    $captureResult['pspReference'],
                    $captureResult['paymentPspReference'],
                    $order->getId(),
                    $amount,
                    $currency,
                    $captureResult['status'] ?? "",
                    Module::ADYEN_ACTION_CAPTURE
                );
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function refundAdyenAmount(): void
    {
        /** @var Order $order */
        $order = $this->getEditObject();
        $pspReference = $order->getFieldData('adyenpspreference');
        $reference = $order->getFieldData('oxordernr');

        $request = Registry::getRequest();
        $amount = (float)$request->getRequestParameter('refund_amount');
        $possibleAmount = $this->getPossibleRefundAmount();
        $amount = min($amount, $possibleAmount);
        $currency = $request->getRequestParameter('refund_currency');

        $paymentService = $this->getServiceFromContainer(PaymentRefund::class);
        $success = $paymentService->doAdyenRefund(
            $amount,
            $pspReference,
            $reference
        );

        if ($success) {
            $refundResult = $paymentService->getRefundResult();

            // everything is fine, we can save the references
            if (isset($refundResult['paymentPspReference'])) {
                $this->setAdyenHistoryEntry(
                    $refundResult['pspReference'],
                    $refundResult['paymentPspReference'],
                    $order->getId(),
                    $amount,
                    $currency,
                    $refundResult['status'] ?? "",
                    Module::ADYEN_ACTION_REFUND
                );
            }
        }
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
                    $this->getPossibleCaptureAmount() > 0
                );
            }
        }
        return $this->isCapturePossible;
    }

    public function isAdyenRefundPossible(): bool
    {
        if (is_null($this->isRefundPossible)) {
            $this->isRefundPossible = (
                $this->isAdyenOrder() &&
                $this->getPossibleRefundAmount() > 0
            );
        }
        return $this->isRefundPossible;
    }

    public function isAdyenCancelPossible(): bool
    {
        if (is_null($this->isCancelPossible)) {
            $this->isCancelPossible = false;
            if ($this->isAdyenOrder()) {
                /** @var Order $order */
                $order = $this->getEditObject();
                $pspReference = $order->getFieldData('adyenpspreference');
                $adyenHistory = oxNew(AdyenHistory::class);
                $canceledAmount = $adyenHistory->getCanceledSum($pspReference);
                $this->isCancelPossible = (
                    $order->isAdyenCancelPossible() &&
                    !$canceledAmount
                );
            }
        }
        return $this->isCancelPossible;
    }

    public function getPossibleCaptureAmount(): float
    {
        $result = 0;
        if ($this->isAdyenOrder()) {
            /** @var Order $order */
            $order = $this->getEditObject();
            $pspReference = $order->getFieldData('adyenpspreference');
            $adyenHistory = oxNew(AdyenHistory::class);
            $capturedAmount = $adyenHistory->getCapturedSum($pspReference);
            $result = (float)$order->getTotalOrderSum() - $capturedAmount;
        }
        return $result;
    }

    public function getPossibleRefundAmount(): float
    {
        $result = 0;
        if ($this->isAdyenOrder()) {
            /** @var Order $order */
            $order = $this->getEditObject();
            $pspReference = $order->getFieldData('adyenpspreference');
            $adyenHistory = oxNew(AdyenHistory::class);
            $refundedAmount = $adyenHistory->getRefundedSum($pspReference);
            $capturedAmount = $adyenHistory->getCapturedSum($pspReference);
            $result = $capturedAmount - $refundedAmount;
        }
        return $result;
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

    protected function setAdyenHistoryEntry(
        string $pspReference,
        string $parentPspReference,
        string $orderId,
        float $amount,
        string $currency,
        string $status,
        string $action
    ): bool {
        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setPSPReference($pspReference);
        $adyenHistory->setParentPSPReference($parentPspReference);
        $adyenHistory->setOrderId($orderId);
        $adyenHistory->setPrice($amount);
        $adyenHistory->setCurrency($currency);
        $adyenHistory->setAdyenStatus($status);
        $adyenHistory->setAdyenAction($action);
        return (bool) $adyenHistory->save();
    }
}
