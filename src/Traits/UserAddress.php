<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidEsales\Eshop\Application\Model\Country as EshopModelCountry;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;

/**
 * Convenience trait to work with JSON-Data
 */
trait UserAddress
{
    use Json;

    public function getAdyenShopperName(): string
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Address|null $address */
        $address = $user->getSelectedAddress();
        /** @var Address|User $dataObj */
        $dataObj = $address ?: $user;
        $result = [
            'firstName' => $dataObj->getAdyenStringData('oxfname'),
            'lastName' => $dataObj->getAdyenStringData('oxlname')
        ];
        return $this->arrayToJson($result);
    }

    public function getAdyenDeliveryAddress(): string
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Address|null $address */
        $address = $user->getSelectedAddress();
        /** @var Address|User $dataObj */
        $dataObj = $address ?: $user;

        /** @var Country $country */
        $country = oxNew(EshopModelCountry::class);
        $country->load($dataObj->getAdyenStringData('oxcountryid'));
        /** @var null|string $countryIso */
        $countryIso = $country->getAdyenStringData('oxisoalpha2');

        $result = [
            'city' => $dataObj->getAdyenStringData('oxcity'),
            'country' => $countryIso,
            'houseNumberOrName' => $dataObj->getAdyenStringData('oxstreetnr'),
            'postalCode' => $dataObj->getAdyenStringData('oxzip'),
            'stateOrProvince' => $dataObj->getAdyenStringData('oxstateid'),
            'street' => $dataObj->getAdyenStringData('oxstreet')
        ];
        return $this->arrayToJson($result);
    }
}
