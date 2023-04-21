<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactory;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\UserRepository::getUserLocale
     * @covers \OxidSolutionCatalysts\Adyen\Service\UserRepository::__construct
     */
    public function testGetUserLocaleUnknownLocale()
    {
        $countryIso = 'DE';
        $userRepo = new UserRepository(
            $this->createQueryBuilderFactoryMock(),
            $this->createContextMock(),
            $this->createConfigMock(),
            $this->createCountryRepositoryMock($countryIso),
            $this->createModuleSettingsMock($countryIso)
        );

        // in first call ModuleSettings::getLocaleForCountryIso is called to get the country iso
        // in second call the internal userLocale array is used
        $this->assertEquals($countryIso, $userRepo->getUserLocale());
        $this->assertEquals($countryIso, $userRepo->getUserLocale());
    }

    private function createQueryBuilderFactoryMock(): QueryBuilderFactory
    {
        return $this->createMock(QueryBuilderFactory::class);
    }

    private function createContextMock(): ContextInterface
    {
        return $this->createMock(ContextInterface::class);
    }

    private function createConfigMock(): Config
    {
        return $this->createMock(Config::class);
    }

    private function createCountryRepositoryMock(string $countryIso): CountryRepository
    {
        $mock = $this->createMock(CountryRepository::class);
        $mock->expects($this->exactly(2))
            ->method('getCountryIso')
            ->willReturn($countryIso);

        return $mock;
    }

    private function createModuleSettingsMock($countryIso): ModuleSettings
    {
        $mock = $this->createMock(ModuleSettings::class);
        $mock->expects($this->once())
            ->method('getLocaleForCountryIso')
            ->willReturn($countryIso);

        return $mock;
    }
}
