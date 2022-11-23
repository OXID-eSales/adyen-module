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
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
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

    protected ?string $adyenPaymentName = null;

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
     */
    public function cancelOrder(): void
    {
        parent::cancelOrder();
        if ($this->isAdyenCancelPossible()) {
            $reference = $this->getFieldData('oxordernr');
            $pspReference = $this->getFieldData('adyenpspreference');

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
                    $adyenHistory->setCurrency($this->getFieldData('oxcurrency'));
                    if (isset($cancelResult['status'])) {
                        $adyenHistory->setAdyenStatus($cancelResult['status']);
                    }
                    $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_CANCEL);
                    $adyenHistory->save();
                }
            }
        }
    }

    public function isAdyenCancelPossible(): bool
    {
        $result = false;
        if ($this->isAdyenOrder()) {
            $pspReference = $this->getFieldData(self::PSPREFERENCEFIELD);
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
    public function isAdyenOrder(): bool
    {
        return (
            Module::isAdyenPayment((string)$this->getFieldData('oxpaymenttype')) &&
            $this->getAdyenPSPReference()
        );
    }

    public function isAdyenOrderPaid(): bool
    {
        return (
            'OK' === $this->getFieldData('oxtransstatus') &&
            !str_contains((string)$this->getFieldData('oxpaid'), '0000')
        );
    }

    public function getAdyenPaymentName(): string
    {
        if (is_null($this->adyenPaymentName)) {
            $paymentId = $this->getFieldData('oxpaymenttype');
            $payment = oxNew(Payment::class);
            $payment->load($paymentId);
            $this->adyenPaymentName = $payment->getFieldData('oxdesc');
        }
        return $this->adyenPaymentName;
    }

    public function getAdyenPSPReference(): string
    {
        return (string) $this->getFieldData(self::PSPREFERENCEFIELD);
    }

    public function setAdyenPSPReference(string $pspReference): void
    {
        $this->assign(
            [
                self::PSPREFERENCEFIELD => $pspReference
            ]
        );
    }

    public function createNumberForAdyenPayment(): string
    {
        $this->_setNumber();
        return (string)$this->getFieldData('oxordernr');
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
}
