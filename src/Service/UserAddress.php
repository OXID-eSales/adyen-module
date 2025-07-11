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
        $dataObj = $user;
        if ($user->getSelectedAddressId()) {
            $dataObj = $user->getSelectedAddress();
        }
        /** @var AdyenAddress|AdyenUser|null $dataObj */
        return [
            'firstName' => $dataObj ? $dataObj->getAdyenStringData('oxfname') : '',
            'lastName' => $dataObj ? $dataObj->getAdyenStringData('oxlname') : ''
        ];
    }

    public function getAdyenDeliveryAddress(User $user): array
    {
        $dataObj = $user;
        if ($user->getSelectedAddressId()) {
            $dataObj = $user->getSelectedAddress();
        }
        /** @var AdyenAddress|AdyenUser|null $dataObj */
        /** @var AdyenCountry $country */
        $country = $this->oxNewService->oxNew(Country::class);
        $countryIso = '';
        if ($dataObj) {
            $country->load($dataObj->getAdyenStringData('oxcountryid'));
            /** @var null|string $countryIso */
            $countryIso = $country->getAdyenStringData('oxisoalpha2');
        }

        return [
            'city' => $dataObj ? $dataObj->getAdyenStringData('oxcity') : '',
            'country' => $countryIso,
            'houseNumberOrName' => $dataObj ? $dataObj->getAdyenStringData('oxstreetnr') : '',
            'postalCode' => $dataObj ? $dataObj->getAdyenStringData('oxzip') : '',
            'stateOrProvince' => $dataObj ? $dataObj->getAdyenStringData('oxstateid') : '',
            'street' => $dataObj ? $dataObj->getAdyenStringData('oxstreet') : ''
        ];
    }
}
