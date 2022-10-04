<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Config;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /**
     * @dataProvider logPathDataProvider
     */
    public function testGetPaymentLogFilePath($configValue): void
    {
        $configStub = $this->createConfiguredMock(Config::class, [
            'getLogsDir' => $configValue
        ]);

        $sut = new Context($configStub);
        $this->assertSame(
            "logsDir/adyen/adyen_" . date("Y-m-d") . ".log",
            $sut->getAdyenLogFilePath()
        );
    }

    public function logPathDataProvider(): array
    {
        return [
            ['logsDir/'],
            ['logsDir']
        ];
    }

    public function testGetCurrentShopId(): void
    {
        $configStub = $this->createConfiguredMock(Config::class, [
            'getShopId' => 10
        ]);

        $sut = new Context($configStub);
        $this->assertSame(10, $sut->getCurrentShopId());
    }

    public function testGetCurrentShopUrl(): void
    {
        $configStub = $this->createConfiguredMock(Config::class, [
            'getCurrentShopUrl' => 'https://test.dev'
        ]);

        $sut = new Context($configStub);
        $this->assertSame('https://test.dev', $sut->getCurrentShopUrl());
    }

    public function testGetActiveCurrencyName(): void
    {
        $currencyName = 'exampleCurrencyName';

        $currency = new \stdClass();
        $currency->name = $currencyName;

        $configStub = $this->createConfiguredMock(Config::class, [
            'getActShopCurrencyObject' => $currency
        ]);

        $sut = new Context($configStub);
        $this->assertSame($currencyName, $sut->getActiveCurrencyName());
    }

    public function testGetActiveCurrencyDecimal(): void
    {
        $currencyDecimal = 2;

        $currency = new \stdClass();
        $currency->decimal = $currencyDecimal;

        $configStub = $this->createConfiguredMock(Config::class, [
            'getActShopCurrencyObject' => $currency
        ]);

        $sut = new Context($configStub);
        $this->assertSame($currencyDecimal, $sut->getActiveCurrencyDecimals());
    }

    public function testGetActiveCurrencySign(): void
    {
        $currencySign = '$';

        $currency = new \stdClass();
        $currency->sign = $currencySign;

        $configStub = $this->createConfiguredMock(Config::class, [
            'getActShopCurrencyObject' => $currency
        ]);

        $sut = new Context($configStub);
        $this->assertSame($currencySign, $sut->getActiveCurrencySign());
    }
}
