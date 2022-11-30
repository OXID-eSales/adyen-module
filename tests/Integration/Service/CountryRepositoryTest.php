<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

final class CountryRepositoryTest extends TestCase
{
    use ServiceContainer;

    public function setup(): void
    {
        parent::setUp();

        foreach ($this->providerTestCountryData() as $dataSet) {
            [$countryId, $countryIsoAlpha2] = $dataSet;
            $country = oxNew(Country::class);
            $country->setId($countryId);
            $country->assign([
                'oxcountry__oxactive' => 1,
                'oxcountry__oxisoalpha2' => $countryIsoAlpha2
            ]);
            $country->save();
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestCountryData() as $dataSet) {
            [$countryId, ] = $dataSet;
            $country = oxNew(Country::class);
            $country->load($countryId);
            $country->delete();
        }
    }

    /**
     * @dataProvider providerTestCountryData
     */
    public function testGetCountryIso(
        string $countryId
    ): void {
        $country = oxNew(Country::class);
        $country->load($countryId);

        /** @var Session $session */
        $session = Registry::getSession();

        // use User to transport a CountryId
        $user = oxNew(User::class);
        $user->assign([
            'oxuser__oxcountryid' => $countryId
        ]);
        $user->save();
        $session->setUser($user);

        $service = $this->getServiceFromContainer(CountryRepository::class);

        $this->assertSame($country->oxcountry__oxisoalpha2->value, $service->getCountryIso());
    }

    public function testGetCountryId(): void
    {
        $config = Registry::getConfig();
        $session = Registry::getSession();
        $service = $this->getServiceFromContainer(CountryRepository::class);

        // Case 1) via DeliveryAddress
        $config->setGlobalParameter('delcountryid', '');
        $session->setVariable('deladrid', 'Dummy');
        $deliveryAddress = oxNew(Address::class);
        $deliveryAddress->setId('Dummy');
        $deliveryAddress->assign([
            'oxaddress__oxcountryid' => 'c789'
        ]);
        $deliveryAddress->save();
        $this->assertSame('c789', $service->getCountryId());

        // Case 2) via User
        $session->setVariable('deladrid', '');
        $user = oxNew(User::class);
        $user->setId('456');
        $user->assign([
            'oxuser__oxactive' => 1,
            'oxuser__oxusername' => 'dummy',
            'oxuser__oxcountryid' => 'c456'
        ]);
        $session->setUser($user);
        $this->assertSame('c456', $service->getCountryId());

        // Case 3) via aHomeCountry - a7c40f631fc920687.20179984 OXID-default HomeCountry
        $user = oxNew(User::class);
        $session->setUser($user);
        $this->assertSame('a7c40f631fc920687.20179984', $service->getCountryId());
    }

    public function providerTestCountryData(): array
    {
        return [
            [
                'c123',
                'DE'
            ],
            [
                'c456',
                'NL'
            ],
            [
                'c789',
                'DK'
            ],
            [
                'c321',
                'US'
            ]
        ];
    }
}
