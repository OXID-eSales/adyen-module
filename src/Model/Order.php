<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\PaymentCapture;
use OxidSolutionCatalysts\Adyen\Service\PaymentRefund;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Order extends Order_parent
{
    use ServiceContainer;

    /**
     * Payment needs redirect
     *
     * @var int
     */
    public const ORDER_STATE_ADYENPAYMENTNEEDSREDICRET = 5000;

    protected const PSPREFERENCEFIELD = 'adyenpspreference';

    protected const ORDERREFERENCEFIELD = 'adyenorderreference';

    protected string $adyenPaymentName = '';

    private QueryBuilderFactoryInterface $queryBuilderFactory;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function init($tableName = null, $forceAllFields = false): void
    {
        parent::init($tableName, $forceAllFields);
        $this->queryBuilderFactory = $this->getServiceFromContainer(QueryBuilderFactoryInterface::class);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function isAdyenOrder(): bool
    {
        return (
            Module::isAdyenPayment($this->getAdyenOrderData('oxpaymenttype')) &&
            $this->getAdyenPSPReference()
        );
    }

    public function isAdyenOrderPaid(): bool
    {
        return (
            'OK' === $this->getFieldData('oxtransstatus') &&
            !str_contains($this->getAdyenOrderData('oxpaid'), '0000')
        );
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function finalizeOrder(Basket $basket, $user, $recalcOrder = false): int
    {
        $result = parent::finalizeOrder($basket, $user, $recalcOrder);
        // the final OrderStatus is set via Notification
        if ($this->isAdyenOrder()) {
            $this->setAdyenOrderStatus('NOT_FINISHED');
        }
        return $result;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function cancelOrder(): void
    {
        parent::cancelOrder();
        if ($this->isAdyenRefundPossible()) {
            $amount = $this->getPossibleRefundAmount();
            $this->refundAdyenOrder($amount);
        } else {
            $this->cancelAdyenOrder();
        }
    }

    public function isAdyenCapturePossible(): bool
    {
        $result = false;
        if ($this->isAdyenOrder()) {
            /** @var \OxidSolutionCatalysts\Adyen\Model\Payment $payment */
            $payment = oxNew(Payment::class);
            $payment->load($this->getAdyenOrderData('oxpaymenttype'));
            $result = (
                $payment->isAdyenManualCapture() &&
                $this->getPossibleCaptureAmount() > 0
            );
        }
        return $result;
    }

    public function isAdyenRefundPossible(): bool
    {
        return (
            $this->isAdyenOrder() &&
            $this->getPossibleRefundAmount() > 0
        );
    }

    public function isAdyenCancelPossible(): bool
    {
        $result = false;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenOrderData(self::PSPREFERENCEFIELD);
            $adyenHistory = oxNew(AdyenHistory::class);
            $lastAction = $adyenHistory->getLastAction($pspReference);
            $result = (
                $lastAction === Module::ADYEN_ACTION_AUTHORIZE
            );
        }
        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function captureAdyenOrder(float $amount): void
    {
        if (!$this->isAdyenCapturePossible()) {
            return;
        }

        $pspReference = $this->getAdyenPspReference();
        $reference = (string)$this->getFloatAdyenOrderData('oxordernr');

        $possibleAmount = $this->getPossibleCaptureAmount();
        $amount = min($amount, $possibleAmount);

        $currency = $this->getAdyenOrderData('oxcurrency');

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
                    $this->getId(),
                    $amount,
                    $currency,
                    $captureResult['status'] ?? "",
                    Module::ADYEN_ACTION_CAPTURE
                );
            }
        }
    }

    public function cancelAdyenOrder(): void
    {
        if (!$this->isAdyenCancelPossible()) {
            return;
        }

        // Adyen References are Strings
        $reference = (string)$this->getFloatAdyenOrderData('oxordernr');
        $pspReference = $this->getAdyenOrderData('adyenpspreference');

        $paymentService = $this->getServiceFromContainer(PaymentCancel::class);
        $success = $paymentService->doAdyenCancel(
            $pspReference,
            $reference
        );

        if ($success) {
            $cancelResult = $paymentService->getCancelResult();

            // everything is fine, we can save the references
            if (isset($cancelResult['paymentPspReference'])) {
                $adyenHistory = oxNew(AdyenHistory::class);
                $adyenHistory->setParentPSPReference($cancelResult['paymentPspReference']);
                $adyenHistory->setPSPReference($cancelResult['pspReference']);
                $adyenHistory->setOrderId($this->getId());
                $adyenHistory->setPrice((float)$this->getTotalOrderSum());
                $adyenHistory->setCurrency($this->getAdyenOrderData('oxcurrency'));
                if (isset($cancelResult['status'])) {
                    $adyenHistory->setAdyenStatus($cancelResult['status']);
                }
                $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_CANCEL);
                $adyenHistory->save();
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function refundAdyenOrder(float $amount): void
    {
        if (!$this->isAdyenRefundPossible()) {
            return;
        }

        $pspReference = $this->getAdyenPspReference();
        $reference = (string)$this->getFloatAdyenOrderData('oxordernr');

        $possibleAmount = $this->getPossibleRefundAmount();
        $amount = min($amount, $possibleAmount);

        $currency = $this->getAdyenOrderData('oxcurrency');

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
                    $this->getId(),
                    $amount,
                    $currency,
                    $refundResult['status'] ?? "",
                    Module::ADYEN_ACTION_REFUND
                );
            }
        }
    }

    public function getPossibleCaptureAmount(): float
    {
        $result = 0;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenPspReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            $capturedAmount = $adyenHistory->getCapturedSum($pspReference);
            $result = (float)$this->getTotalOrderSum() - $capturedAmount;
        }
        return $result;
    }

    public function getPossibleRefundAmount(): float
    {
        $result = 0;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenPspReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            $refundedAmount = $adyenHistory->getRefundedSum($pspReference);
            $capturedAmount = $adyenHistory->getCapturedSum($pspReference);
            $result = $capturedAmount - $refundedAmount;
        }
        return $result;
    }

    public function getAdyenPaymentName(): string
    {
        if (!$this->adyenPaymentName && $this->isAdyenOrder()) {
            $paymentId = $this->getAdyenOrderData('oxpaymenttype');
            $payment = oxNew(Payment::class);
            $payment->load($paymentId);
            /** @var null|string $desc */
            $desc = $payment->getFieldData('oxdesc');
            $this->adyenPaymentName = $desc ?? '';
        }
        return $this->adyenPaymentName;
    }

    public function getAdyenPSPReference(): string
    {
        return $this->getAdyenOrderData('adyenpspreference');
    }

    public function setAdyenPSPReference(string $pspReference): void
    {
        $this->assign(
            [
                self::PSPREFERENCEFIELD => $pspReference
            ]
        );
    }

    public function getAdyenOrderReference(): string
    {
        $orderReference = $this->getAdyenOrderData('adyenorderreference');
        if (!$orderReference) {
            $orderReference = Registry::getUtilsObject()->generateUId();
            $this->setAdyenOrderReference($orderReference);
        }
        return $orderReference;
    }

    /**
     * We need a reference for Adyen like the oxordernr. But even before the order even exists.
     * Therefore, it should be possible to create a clear reference beforehand and then save it
     * in the order later
     */
    public function setAdyenOrderReference(string $orderReference): void
    {
        $this->assign(
            [
                self::ORDERREFERENCEFIELD => $orderReference
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function markAdyenOrderAsPaid(): void
    {
        $this->setAdyenOrderStatus('OK');

        $utilsDate = Registry::getUtilsDate();
        $date = date('Y-m-d H:i:s', $utilsDate->getTime());

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder->update($this->getCoreTableName())
            ->set('oxpaid', ':date')
            ->where('oxid = :oxid');

        $parameters = [
            'date' => $date,
            'oxid' => $this->getId()
        ];

        $queryBuilder->setParameters($parameters)
            ->execute();

        //updating order object
        $this->assign([
            'oxorder__oxpaid' => $date
        ]);
    }

    public function setAdyenOrderStatus(string $status): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder->update($this->getCoreTableName())
            ->set('oxtransstatus', ':status')
            ->where('oxid = :oxid');

        $parameters = [
            'oxid' => $this->getId(),
            'status' => $status
        ];

        $queryBuilder->setParameters($parameters)
            ->execute();

        //updating order object
        $this->assign([
            'oxorder__oxtransstatus' => $status
        ]);
    }

    public function delete($oxid = null): bool
    {
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenPSPReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            if ($adyenHistory->loadByPSPReference($pspReference)) {
                $adyenHistory->delete();
            }
        }
        return parent::delete($oxid);
    }



    protected function getAdyenOrderData(string $key): string
    {
        /** @var null|string $value */
        $value = $this->getFieldData($key);
        return $value ?? '';
    }

    protected function getFloatAdyenOrderData(string $key): float
    {
        /** @var null|float $value */
        $value = $this->getFieldData($key);
        return is_float($value) ? $value : 0;
    }

    public function setAdyenHistoryEntry(
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
