<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Service\PaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use PHPUnit\Framework\TestCase;

class PaymentMethodsCollectTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentMethods::collectAdyenPaymentMethods
     */
    public function testCollectAdyenPaymentMethods(): void
    {
        $currencyName = 'EUR';
        $currencyFilterAmount = '1000';
        $countryIso = 'DE';
        $shopperLocale = 'de_DE';
        $merchantAccount = 'merchant_account';
        $currencyDecimals = 2;

        $paymentMethods = $this->createAdyenAPIPaymentMethodsMock(
            $currencyName,
            $currencyFilterAmount,
            $countryIso,
            $shopperLocale,
            $merchantAccount
        );
        $paymentMethodsResponse = $this->createAdyenAPIResponsePaymentMethodsMock($paymentMethods);
        $oxNewService = $this->createOxNewServiceMock($paymentMethods);
        $context = $this->createContextMock($currencyName, $currencyDecimals);
        $modulesSettings = $this->createModuleSettingsMock($merchantAccount);
        $userRepo = $this->createUserRepoMock($shopperLocale);
        $countryRepo = $this->createCountryRepoMock($countryIso);

        $paymentMethodService = new PaymentMethods(
            $context,
            $modulesSettings,
            $paymentMethodsResponse,
            $userRepo,
            $countryRepo,
            $oxNewService
        );

        $actual = $paymentMethodService->collectAdyenPaymentMethods();

        $this->assertEquals($paymentMethodsResponse, $actual);
    }

    private function createCountryRepoMock(string $countryIso): CountryRepository
    {
        $mock = $this->getMockBuilder(CountryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getCountryIso',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getCountryIso')
            ->willReturn($countryIso);

        return $mock;
    }

    private function createOxNewServiceMock(AdyenAPIPaymentMethods $paymentMethods): OxNewService
    {
        $mock = $this->getMockBuilder(OxNewService::class)
            ->onlyMethods(
                [
                    'oxNew',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('oxNew')
            ->with(AdyenAPIPaymentMethods::class)
            ->willReturn($paymentMethods);

        return $mock;
    }

    private function createAdyenAPIResponsePaymentMethodsMock(
        AdyenAPIPaymentMethods $paymentMethods
    ): AdyenAPIResponsePaymentMethods {
        $mock = $this->getMockBuilder(AdyenAPIResponsePaymentMethods::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'loadAdyenPaymentMethods',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('loadAdyenPaymentMethods')
            ->with($paymentMethods);

        return $mock;
    }

    private function createModuleSettingsMock(string $merchantAccount): ModuleSettings
    {
        $mock = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getMerchantAccount',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getMerchantAccount')
            ->willReturn($merchantAccount);

        return $mock;
    }

    private function createUserRepoMock(string $userLocale): UserRepository
    {
        $mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getUserLocale',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getUserLocale')
            ->willReturn($userLocale);

        return $mock;
    }

    private function createContextMock(
        string $currencyName,
        int $currencyDecimals
    ): Context {
        $mock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getActiveCurrencyName',
                    'getActiveCurrencyDecimals',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('getActiveCurrencyName')
            ->willReturn($currencyName);

        $mock->expects($this->once())
            ->method('getActiveCurrencyDecimals')
            ->willReturn($currencyDecimals);

        return $mock;
    }

    private function createAdyenAPIPaymentMethodsMock(
        string $currencyName,
        string $currencyFilterAmount,
        string $countryIso,
        string $shopperLocale,
        string $merchantAccount
    ): AdyenAPIPaymentMethods {
        $mock = $this->getMockBuilder(AdyenAPIPaymentMethods::class)
            ->onlyMethods(
                [
                    'setCurrencyName',
                    'setCurrencyFilterAmount',
                    'setCountryCode',
                    'setShopperLocale',
                    'setMerchantAccount',
                ]
            )
            ->getMock();

        $mock->expects($this->once())
            ->method('setCurrencyName')
            ->with($currencyName);

        $mock->expects($this->once())
            ->method('setCurrencyFilterAmount')
            ->with($currencyFilterAmount);

        $mock->expects($this->once())
            ->method('setCountryCode')
            ->with($countryIso);

        $mock->expects($this->once())
            ->method('setShopperLocale')
            ->with($shopperLocale);

        $mock->expects($this->once())
            ->method('setMerchantAccount')
            ->with($merchantAccount);

        return $mock;
    }
}
