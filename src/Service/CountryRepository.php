<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Application\Model\Address as EshopModelAddress;
use OxidEsales\Eshop\Application\Model\Country as EshopModelCountry;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\User;

/**
 * @extendable-class
 */
class CountryRepository
{
    /** @var Config */
    private Config $config;

    /** @var SessionSettings */
    private SessionSettings $session;

    protected array $countryIso = [];

    public function __construct(
        Config $config,
        SessionSettings $session
    ) {
        $this->config = $config;
        $this->session = $session;
    }

    public function getCountryIso(): string
    {
        $countryId = $this->getCountryId();
        if (!isset($this->countryIso[$countryId])) {
            /** @var Country $country */
            $country = oxNew(EshopModelCountry::class);
            $country->load($countryId);
            /** @var null|string $countryIso */
            $countryIso = $country->getAdyenStringData('oxisoalpha2');
            if (!is_null($countryIso)) {
                $this->countryIso[$countryId] = $countryIso;
            }
        }
        return $this->countryIso[$countryId];
    }

    /**
     * Tries to fetch user country ID
     *
     * @return string
     */
    public function getCountryId(): string
    {
        $countryId = $this->config->getGlobalParameter('delcountryid');

        // try from Session Delivery-Address
        if (!$countryId) {
            $addressId = $this->session->getDeliveryId();
            /** @var Address $deliveryAddress */
            $deliveryAddress = oxNew(EshopModelAddress::class);
            $countryId = $deliveryAddress->load($addressId) ?
                $deliveryAddress->getAdyenStringData('oxcountryid') :
                '';
        }

        // try from Session Invoice Address
        if (!$countryId) {
            /** @var User $user */
            $user = $this->session->getUser();
            $countryId = $user->getAdyenStringData('oxcountryid');
        }

        // try global shop config
        if (!$countryId) {
            $homeCountry = $this->config->getConfigParam('aHomeCountry');
            $countryId = is_array($homeCountry) ? current($homeCountry) : '';
        }

        return $countryId;
    }
}
