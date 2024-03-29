<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Model\Address as AdyenAddress;
use OxidSolutionCatalysts\Adyen\Model\User as AdyenUser;
use OxidSolutionCatalysts\Adyen\Model\Country as AdyenCountry;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\User;

/**
 * @extendable-class
 */
class CountryRepository
{
    private Config $config;
    private SessionSettings $session;
    private OxNewService $oxNewService;

    protected array $countryIso = [];

    public function __construct(
        Config $config,
        SessionSettings $session,
        OxNewService $oxNewService
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->oxNewService = $oxNewService;
    }

    public function getCountryIso(): string
    {
        $countryId = $this->getCountryId();
        if (!isset($this->countryIso[$countryId])) {
            /** @var Country $country */
            $country = $this->oxNewService->oxNew(Country::class);
            $country->load($countryId);
            /** @var null|string $countryIso */
            /** @var AdyenCountry $country */
            $countryIso = $country->getAdyenStringData('oxisoalpha2');
            $this->countryIso[$countryId] = $countryIso;
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
            /** @var AdyenAddress $deliveryAddress */
            $deliveryAddress = $this->oxNewService->oxNew(Address::class);
            $countryId = $deliveryAddress->load($addressId) ?
                $deliveryAddress->getAdyenStringData('oxcountryid') :
                '';
        }

        // try from Session Invoice Address
        if (!$countryId) {
            /** @var User $user */
            $user = $this->session->getUser();
            /** @var AdyenUser $user */
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
