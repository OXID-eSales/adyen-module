<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenHistory extends BaseModel
{
    use ServiceContainer;

    protected const PSPREFERENCEFIELD = 'pspreference';
    protected const PSPPARENTREFERENCEFIELD = 'pspparentreference';

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ContextInterface */
    private $context;

    /** @var Config */
    private $config;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'OxidSolutionCatalysts\Adyen\Model\AdyenHistory'; //NOSONAR

    /**
     * Core table name
     *
     * @var string
     */
    protected $_sCoreTable = Module::ADYEN_HISTORY_TABLE; //NOSONAR

    public function __construct()
    {
        parent::__construct();
        $this->init($this->_sCoreTable);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function init($tableName = null, $forceAllFields = false): void
    {
        parent::init($tableName, $forceAllFields);
        $this->queryBuilderFactory = $this->getServiceFromContainer(QueryBuilderFactoryInterface::class);
        $this->context = $this->getServiceFromContainer(ContextInterface::class);

        $this->config = Registry::getConfig();
    }

    public function loadByPSPReference(string $pspReference): bool
    {
        $result = false;

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder->select('oxid')
            ->from($this->getCoreTableName())
            ->setMaxResults(1)
            ->where(self::PSPREFERENCEFIELD . ' = :pspreference');

        $parameters = [
            'pspreference' => $pspReference
        ];

        if (!$this->config->getConfigParam('blMallUsers')) {
            $queryBuilder->andWhere('oxshopid = :oxshopid');
            $parameters['oxshopid'] = $this->context->getCurrentShopId();
        }

        /** @var Result $resultDB */
        $resultDB = $queryBuilder->setParameters($parameters)
            ->execute();

        if (is_a($resultDB, Result::class)) {
            $dbData = $resultDB->fetchOne();
            $result = $this->load($dbData['oxid']);
        }
        return $result;
    }

    public function delete($oxid = null): bool
    {
        if ($oxid) {
            $this->load($oxid);
        }
        $pspReference = $this->getAdyenPSPReference();
        $this->deleteChildReferences($pspReference);

        return parent::delete($oxid);
    }

    public function getAdyenPSPReference(): string
    {
        return (string) $this->getFieldData(self::PSPREFERENCEFIELD);
    }

    public function getAdyenPSPParentReference(): string
    {
        return (string) $this->getFieldData(self::PSPPARENTREFERENCEFIELD);
    }

    /**
     * Deletes child records
     *
     * @param string $pspReference AdyenHistory PSP Reference
     */
    protected function deleteChildReferences($pspReference): void
    {
        if ($pspReference) {

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $this->queryBuilderFactory->create();

            $queryBuilder->select('oxid')
                ->from($this->getCoreTableName())
                ->where(self::PSPPARENTREFERENCEFIELD . ' = :pspreference');

            $parameters = [
                'pspreference' => $pspReference
            ];

            if (!$this->config->getConfigParam('blMallUsers')) {
                $queryBuilder->andWhere('oxshopid = :oxshopid');
                $parameters['oxshopid'] = $this->context->getCurrentShopId();
            }

            /** @var Result $resultDB */
            $resultDB = $queryBuilder->setParameters($parameters)
                ->execute();

            if (is_a($resultDB, Result::class)) {
                $fromDB = $resultDB->fetchAllAssociative();
                foreach ($fromDB as $row) {
                    $adyenHistory = oxNew(AdyenHistory::class);
                    $adyenHistory->delete($row['oxid']);
                }
            }
        }
    }

    /**
     * @param string $oxorderid
     * @return bool
     */
    public function loadByOxOrderId(string $oxorderid): bool
    {
        $this->_addField('oxorderid', 0);
        $query = $this->buildSelectString([$this->getViewName() . '.oxorderid' => $oxorderid]);
        $this->_isLoaded = $this->assignRecord($query);

        return $this->_isLoaded;
    }

    public function getOrderId(): string
    {
        return (string) $this->getFieldData('orderid');
    }

    public function getPSPReference(): string
    {
        return (string) $this->getFieldData('pspreference');
    }

    public function getParentPSPReference(): string
    {
        return (string) $this->getFieldData('parentpspreference');
    }

    public function getOxPrice(): float
    {
        return (float) $this->getFieldData('oxprice');
    }

    public function getAdyenStatus(): string
    {
        return (string) $this->getFieldData('adyenstatus');
    }

    public function setPSPReference(string $pspreference): void
    {
        $this->assign(
            [
                'pspreference' => $pspreference
            ]
        );
    }

    public function setOrderId(string $orderId): void
    {
        $this->assign(
            [
                'oxorderid' => $orderId
            ]
        );
    }

    public function setParentPSPReference(string $parentpspreference): void
    {
        $this->assign(
            [
                'parentpspreference' => $parentpspreference
            ]
        );
    }

    public function setPrice(float $oxprice): void
    {
        $this->assign(
            [
                'oxprice' => $oxprice
            ]
        );
    }

    public function setTimeStamp(string $oxtimestamp): void
    {
        $this->assign(
            [
                'oxtimestamp' => $oxtimestamp
            ]
        );
    }

    public function setAdyenStatus(string $adyenstatus): void
    {
        $this->assign(
            [
                'adyenstatus' => $adyenstatus
            ]
        );
    }
}
