<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;


use OxidEsales\Eshop\Application\Model\User;
use OxidSolutionCatalysts\Adyen\Model\User as AdyenUser;
use OxidSolutionCatalysts\Adyen\Model\Address as AdyenAddress;
use OxidEsales\Eshop\Application\Model\Country;
use OxidSolutionCatalysts\Adyen\Model\Country as AdyenCountry;
use OxidSolutionCatalysts\Adyen\Traits\Json;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * Convenience trait to work with JSON-Data
 */
class UserAddress
{
    use Json;
    use ServiceContainer;

    private OxNewService $oxNewService;

    public function __construct(OxNewService $oxNewService)
    {
        $this->oxNewService = $oxNewService;
    }

    public function getAdyenShopperEmail(User $user): string
    {
        /** @var AdyenUser $user */
        return $user->getAdyenStringData('oxusername');
    }

    public function getAdyenShopperName(User $user): array
    {
        /** @var AdyenUser|null $address */
        $address = $user->getSelectedAddress();
        /** @var AdyenAddress|AdyenUser $dataObj */
        $dataObj = $address ?: $user;

        return [
            'firstName' => $dataObj->getAdyenStringData('oxfname'),
            'lastName' => $dataObj->getAdyenStringData('oxlname')
        ];
    }

    public function getAdyenDeliveryAddress(User $user): array
    {
        /** @var AdyenAddress|null $address */
        $address = $user->getSelectedAddress();
        /** @var AdyenAddress|AdyenUser $dataObj */
        $dataObj = $address ?: $user;

        /** @var AdyenCountry $country */
        $country = $this->oxNewService->oxNew(Country::class);
        $country->load($dataObj->getAdyenStringData('oxcountryid'));
        /** @var null|string $countryIso */
        $countryIso = $country->getAdyenStringData('oxisoalpha2');

        return [
            'city' => $dataObj->getAdyenStringData('oxcity'),
            'country' => $countryIso,
            'houseNumberOrName' => $dataObj->getAdyenStringData('oxstreetnr'),
            'postalCode' => $dataObj->getAdyenStringData('oxzip'),
            'stateOrProvince' => $dataObj->getAdyenStringData('oxstateid'),
            'street' => $dataObj->getAdyenStringData('oxstreet')
        ];
    }
}
