<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase
{
    use ServiceContainer;

    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestUserData() as $dataSet) {
            [$userId, $userName, $userPassword, $userCountryId] = $dataSet;
            $user = oxNew(User::class);
            $user->setId($userId);
            $user->assign([
                'oxuser__oxactive' => 1,
                'oxuser__oxusername' => $userName,
                'oxuser__oxcountryid' => $userCountryId
            ]);
            $user->setPassword($userPassword);
            $user->save();
        }

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
        foreach ($this->providerTestUserData() as $dataSet) {
            [$userId, , , ] = $dataSet;
            $user = oxNew(User::class);
            $user->load($userId);
            $user->delete();
        }
        foreach ($this->providerTestCountryData() as $dataSet) {
            [$countryId, ] = $dataSet;
            $country = oxNew(Country::class);
            $country->load($countryId);
            $country->delete();
        }
    }

    /**
     * @dataProvider providerTestUserData
     */
    public function testUserAccountExists(
        string $userId,
        string $userName,
        string $userPassword,
        string $userCountryId
    ): void {


        $service = $this->getServiceFromContainer(UserRepository::class);
        $this->assertSame((bool)$userPassword, $service->userAccountExists($userName));
    }

    /**
     * @dataProvider providerTestUserData
     */
    public function testGuestAccountExists(
        string $userId,
        string $userName,
        string $userPassword,
        string $userCountryId
    ): void {
        $service = $this->getServiceFromContainer(UserRepository::class);
        $this->assertSame(!$userPassword, $service->guestAccountExists($userName));
    }

    /**
     * @dataProvider providerTestUserData
     */
    public function testGetUserCountryIso(
        string $userId,
        string $userName,
        string $userPassword,
        string $userCountryId
    ): void {
        $session = Registry::getSession();
        $user = oxNew(User::class);
        $user->load($userId);
        $session->setUser($user);

        $country = oxNew(Country::class);
        $country->load($userCountryId);

        $service = $this->getServiceFromContainer(UserRepository::class);
        $this->assertSame($country->oxcountry__oxisoalpha2->value, $service->getUserCountryIso());
    }

    public function testGetCountryId(): void
    {
        $config = Registry::getConfig();
        $session = Registry::getSession();
        $service = $this->getServiceFromContainer(UserRepository::class);

        // Case 1) via GlobalParameter delcountryid
        $config->setGlobalParameter('delcountryid', 'c456');
        $this->assertSame('c456', $service->getCountryId());

        // Case 2) via DeliveryAddress
        $config->setGlobalParameter('delcountryid', '');
        $session->setVariable('deladrid', 'Dummy');
        $deliveryAddress = oxNew(Address::class);
        $deliveryAddress->setId('Dummy');
        $deliveryAddress->assign([
            'oxaddress__oxcountryid' => 'c789'
        ]);
        $deliveryAddress->save();
        $this->assertSame('c789', $service->getCountryId());

        // Case 3) via User
        $session->setVariable('deladrid', '');
        $user = oxNew(User::class);
        $user->load('456');
        $session->setUser($user);
        $this->assertSame('c456', $service->getCountryId());

        // Case 4) via aHomeCountry - a7c40f631fc920687.20179984 OXID-default HomeCountry
        $user = oxNew(User::class);
        $session->setUser($user);
        $this->assertSame('a7c40f631fc920687.20179984', $service->getCountryId());
    }

    public function providerTestUserData(): array
    {
        return [
            [
                '123',
                'accountuser@dummy.dev',
                'accountuser',
                'c123'
            ],
            [
                '456',
                'guestuser@dummy.dev',
                '',
                'c456'
            ]
        ];
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
