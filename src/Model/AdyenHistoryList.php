<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenHistoryList extends ListModel
{
    use ServiceContainer;

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'OxidSolutionCatalysts\Adyen\Model\AdyenHistory'; //NOSONAR

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ContextInterface */
    private $context;

    /** @var Config */
    private $config;

    /**
     * Class Constructor
     *
     * @param string $sObjectName Associated list item object type
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($sObjectName = null)
    {
        parent::__construct($sObjectName);

        $this->queryBuilderFactory = $this->getServiceFromContainer(QueryBuilderFactoryInterface::class);
        $this->context = $this->getServiceFromContainer(ContextInterface::class);
        $this->config = Registry::getConfig();
    }

    /**
     * @return void
     */
    public function getAdyenHistoryList(string $orderId, string $orderDirection = 'asc'): void
    {
        /** @var QueryBuilder $queryBuilder */

        $queryBuilder = $this->queryBuilderFactory->create();

        $listObject = $this->getBaseObject();

        $queryBuilder->select($listObject->getSelectFields())
            ->from(Module::ADYEN_HISTORY_TABLE)
            ->where('oxorderid = :orderid')
            ->orderBy('oxtimestamp', $orderDirection);

        $parameters = [
            'orderid' => $orderId
        ];

        if (!$this->config->getConfigParam('blMallUsers')) {
            $queryBuilder->andWhere('oxshopid = :oxshopid');
            $parameters['oxshopid'] = $this->context->getCurrentShopId();
        }

        /** @var Result $resultDB */
        $resultDB = $queryBuilder->setParameters($parameters)
            ->execute();

        if (is_a($resultDB, Result::class)) {
            $dbData = $resultDB->fetchAllAssociative();
            $this->assignArray($dbData);
        }
    }

    /**
     * @param string $pspReference
     * @return string
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOxidOrderIdByPSPReference(string $pspReference): string
    {
        $oxId = '';

        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder->select('oxorderid')
            ->from(Module::ADYEN_HISTORY_TABLE)
            ->where('pspreference = :pspreference')
            ->orWhere('parentpspreference = :parentpspreference');

        $parameters = [
            'pspreference' => $pspReference,
            'parentpspreference' => $pspReference
        ];

        if (!$this->config->getConfigParam('blMallUsers')) {
            $queryBuilder->andWhere('oxshopid = :oxshopid');
            $parameters['oxshopid'] = $this->context->getCurrentShopId();
        }

        /** @var Result $resultDB */
        $resultDB = $queryBuilder
            ->setParameters($parameters)
            ->setMaxResults(1)
            ->execute();

        if (is_a($resultDB, Result::class)) {
            $oxId = $resultDB->fetchOne();
            $oxId = is_string($oxId) ? $oxId : '';
        }
        return $oxId;
    }
}
