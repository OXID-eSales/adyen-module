<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\Admin\OrderList
 */
class OrderList extends OrderList_parent
{
    use ServiceContainer;

    /**
     * Executes parent method parent::render() and returns name of template
     * file "order_list.html.twig".
     *
     * @return string
     */
    public function render()
    {
        $result = parent::render();

        $viewData = $this->getViewData();
        $viewData["asearch"] = array_merge($viewData["asearch"], [
            Module::ADYEN_HISTORY_TABLE => 'PSPREFERENCE'
        ]);
        $this->setViewData($viewData);

        return $result;
    }

    /**
     * OXID-Core
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function buildSelectString($listObject = null)
    {
        $request = Registry::getRequest();
        /** @var null|string $searchField */
        $searchField = $request->getRequestEscapedParameter('addsearchfld');
        /** @var null|string $searchQuery */
        $searchQuery = $request->getRequestEscapedParameter('addsearch');
        $searchQuery = $searchQuery ?? '';

        $tableName = Module::ADYEN_HISTORY_TABLE;

        if ($tableName !== $searchField || is_null($listObject)) {
            return parent::buildSelectString($listObject);
        }

        /** @var BaseModel $listObject */
        $query = $listObject->buildSelectString();
        $database = DatabaseProvider::getDb();

        $queryPart = "oxorder
            left join {$tableName}
            on {$tableName}.oxorderid = oxorder.oxid
            where ({$tableName}.pspreference like " . $database->quote("%{$searchQuery}%") . "
            or oxorder.adyenorderreference like " . $database->quote("%{$searchQuery}%") . " ) and ";
        return str_replace('oxorder where', $queryPart, $query);
    }
}
