<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use OxidEsales\Eshop\Core\Model\BaseModel as EshopBaseModel;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

//NOTE: later we will do this on module installation, for now on first activation
class StaticContents
{
    private QueryBuilderFactoryInterface $queryBuilderFactory;

    private ModuleSettings $moduleSettings;

    private OxNewService $oxNewService;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ModuleSettings $moduleSettings,
        OxNewService $oxNewService
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->moduleSettings = $moduleSettings;
        $this->oxNewService = $oxNewService;
    }

    public function ensurePaymentMethods(): void
    {
        $activePayments = $this->moduleSettings->getActivePayments();
        foreach (Module::PAYMENT_DEFINTIONS as $paymentId => $paymentDefinitions) {
            $paymentMethod = $this->oxNewService->oxNew(EshopModelPayment::class);
            if ($paymentMethod->load($paymentId) && in_array($paymentId, $activePayments)) {
                $paymentMethod->assign([
                    'oxpayments__oxactive' => true
                ]);
                $paymentMethod->save();
                continue;
            }
            $this->createPaymentMethod($paymentId, $paymentDefinitions);
            $this->assignPaymentToActiveDeliverySets($paymentId);
        }
    }

    protected function assignPaymentToActiveDeliverySets(string $paymentId): void
    {
        $deliverySetIds = $this->getActiveDeliverySetIds();
        foreach ($deliverySetIds as $deliverySetId) {
            $this->assignPaymentToDelivery($paymentId, $deliverySetId);
        }
    }

    protected function assignPaymentToDelivery(string $paymentId, string $deliverySetId): void
    {
        $object2Paymentent = $this->oxNewService->oxNew(EshopBaseModel::class);
        $object2Paymentent->init('oxobject2payment');
        $object2Paymentent->assign(
            [
                'oxpaymentid' => $paymentId,
                'oxobjectid' => $deliverySetId,
                'oxtype' => 'oxdelset'
            ]
        );
        $object2Paymentent->save();
    }

    protected function createPaymentMethod(string $paymentId, array $definitions): void
    {
        /** @var EshopModelPayment $paymentModel */
        $paymentModel = $this->oxNewService->oxNew(EshopModelPayment::class);
        $paymentModel->setId($paymentId);

        $iso2LanguageId = array_flip($this->getLanguageIds());

        $paymentModel->assign(
            [
                'oxactive' => false,
                'oxfromamount' => (int) $definitions['constraints']['oxfromamount'],
                'oxtoamount' => (int) $definitions['constraints']['oxtoamount'],
                'oxaddsumtype' => (string) $definitions['constraints']['oxaddsumtype'],
                'oxsort' => (int) $definitions['sort'],
            ]
        );
        $paymentModel->save();

        foreach ($definitions['descriptions'] as $langAbbr => $data) {
            if (isset($iso2LanguageId[$langAbbr])) {
                $paymentModel->loadInLang((int)$iso2LanguageId[$langAbbr], $paymentModel->getId());
                $paymentModel->assign(
                    [
                        'oxdesc' => $data['desc'],
                        'oxlongdesc' => $data['longdesc']
                    ]
                );
            }
            $paymentModel->save();
        }
    }

    protected function getActiveDeliverySetIds(): array
    {
        $result = [];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->queryBuilderFactory->create();

        /** @var Result $resultDB */
        $resultDB = $queryBuilder
            ->select('oxid')
            ->from('oxdeliveryset')
            ->where('oxactive = 1')
            ->execute();

        if (is_a($resultDB, Result::class)) {
            $fromDB = $resultDB->fetchAllAssociative();
            foreach ($fromDB as $row) {
                $result[$row['oxid']] = $row['oxid'];
            }
        }

        return $result;
    }

    /**
     * get the language-IDs
     */
    protected function getLanguageIds(): array
    {
        return EshopRegistry::getLang()->getLanguageIds();
    }
}
