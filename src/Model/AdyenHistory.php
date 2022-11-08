<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidSolutionCatalysts\Adyen\Core\Module;

class AdyenHistory extends BaseModel
{
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

    public function loadByOxOrderId(string $oxorderid)
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

    public function getOxPrice(): double
    {
        return (double) $this->getFieldData('oxprice');
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

    public function setOrderId(string $orderId)
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

    public function setOxPrice($oxprice): void
    {
        $this->assign(
            [
                'oxprice' => $oxprice
            ]
        );
    }

    public function setOxTimeStamp($oxtimestamp): void
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
