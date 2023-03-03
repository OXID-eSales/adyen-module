<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidSolutionCatalysts\Adyen\Service\OrderIsAdyenCapturePossibleService;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\PaymentCancel;
use OxidSolutionCatalysts\Adyen\Service\PaymentCapture;
use OxidSolutionCatalysts\Adyen\Service\PaymentRefund;
use OxidSolutionCatalysts\Adyen\Service\Module as ModuleService;
use OxidSolutionCatalysts\Adyen\Traits\DataGetter;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Order extends Order_parent
{
    use ServiceContainer;
    use DataGetter;

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
        $moduleService = $this->getServiceFromContainer(ModuleService::class);

        return (
            $moduleService->isAdyenPayment($this->getAdyenStringData('oxpaymenttype')) &&
            $this->getAdyenPSPReference()
        );
    }

    public function isAdyenOrderPaid(): bool
    {
        return (
            'OK' === $this->getAdyenStringData('oxtransstatus') &&
            !str_contains($this->getAdyenStringData('oxpaid'), '0000')
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
        $capturePossibleService = $this->getServiceFromContainer(
            OrderIsAdyenCapturePossibleService::class
        );

        return $this->isAdyenOrder()
            && $this->getPossibleCaptureAmount() > 0.0
            && $capturePossibleService->isAdyenCapturePossible($this->getId());
    }

    public function isAdyenManualCapture(): bool
    {
        $result = false;
        if ($this->isAdyenOrder()) {
            /** @var Payment $payment */
            $payment = oxNew(EshopModelPayment::class);
            $payment->load($this->getAdyenStringData('oxpaymenttype'));
            $result = $payment->isAdyenManualCapture();
        }

        return $result;
    }

    public function isAdyenRefundPossible(): bool
    {
        return (
            $this->isAdyenOrder() &&
            $this->getPossibleRefundAmount() > 0.0
        );
    }

    public function isAdyenCancelPossible(): bool
    {
        return $this->getCapturedAmount() === 0.0;
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
        $reference = $this->getAdyenOrderReference();

        $possibleAmount = $this->getPossibleCaptureAmount();
        $amount = min($amount, $possibleAmount);

        $currency = $this->getAdyenStringData('oxcurrency');

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

        $pspReference = $this->getAdyenStringData('adyenpspreference');
        $reference = $this->getAdyenOrderReference();

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
                $adyenHistory->setCurrency($this->getAdyenStringData('oxcurrency'));
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
        $reference = $this->getAdyenOrderReference();

        $possibleAmount = $this->getPossibleRefundAmount();
        $amount = min($amount, $possibleAmount);

        $currency = $this->getAdyenStringData('oxcurrency');

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

    public function getCapturedAmount(): float
    {
        $result = 0.0;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenPspReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            $result = $adyenHistory->getCapturedSum($pspReference);
        }
        return $result;
    }

    public function getPossibleCaptureAmount(): float
    {
        $result = 0.0;
        if ($this->isAdyenOrder()) {
            $result = (float)$this->getTotalOrderSum() - $this->getCapturedAmount();
        }
        return $result;
    }

    public function getRefundedAmount(): float
    {
        $result = 0.0;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getAdyenPspReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            $result = $adyenHistory->getRefundedSum($pspReference);
        }
        return $result;
    }
    public function getPossibleRefundAmount(): float
    {
        $result = 0.0;
        if ($this->isAdyenOrder()) {
            $result = $this->getCapturedAmount() - $this->getRefundedAmount();
        }
        return $result;
    }

    public function getAdyenPaymentName(): string
    {
        if (!$this->adyenPaymentName && $this->isAdyenOrder()) {
            $paymentId = $this->getAdyenStringData('oxpaymenttype');
            /** @var Payment $payment */
            $payment = oxNew(EshopModelPayment::class);
            $payment->load($paymentId);
            /** @var null|string $desc */
            $desc = $payment->getAdyenStringData('oxdesc');
            $this->adyenPaymentName = $desc ?? '';
        }
        return $this->adyenPaymentName;
    }

    public function getAdyenPSPReference(): string
    {
        return $this->getAdyenStringData('adyenpspreference');
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
        return $this->getAdyenStringData('adyenorderreference');
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
        if (
            (
                $oxid &&
                !$this->load($oxid)
            ) ||
            !$this->isLoaded()
        ) {
             return false;
        }

        if ($this->isAdyenOrder()) {
            if (!$this->isAdyenCancelPossible()) {
                Registry::getUtilsView()->addErrorToDisplay('OSC_ADYEN_CANCEL_NOT_POSSIBLE');
                return false;
            }
            $this->cancelAdyenOrder();
            // delete AdyenHistory
            $pspReference = $this->getAdyenPSPReference();
            $adyenHistory = oxNew(AdyenHistory::class);
            if ($adyenHistory->loadByPSPReference($pspReference)) {
                $adyenHistory->delete();
            }
        }
        return parent::delete($oxid);
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
