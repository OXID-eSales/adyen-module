<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Facts\Facts;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;
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

    /**
     * @cavers \OxidSolutionCatalysts\Adyen\Service\Context::getWebhookControllerUrl
     */
    public function testGetWebhookControllerUrl()
    {
        $contextService = new Context($this->createConfigMock());
        $actualUrl = $contextService->getWebhookControllerUrl();
        $expectedUrl = (new Facts())->getShopUrl() . 'index.php?cl=AdyenWebhookController&shp=1';

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testGetPaymentReturnUrl()
    {
        $sessionChallengeToken = 'sct';
        $sDeliveryAddressMD5 = 'da';
        $pspReference = 'pspR';
        $resultCode = 'rc';
        $amountCurrency = 'ac';

        $actualUrl = (new Context($this->createConfigMock(0)))->getPaymentReturnUrl(
            $sessionChallengeToken,
            $sDeliveryAddressMD5,
            $pspReference,
            $resultCode,
            $amountCurrency
        );
        $expectedUrl = (new Facts())->getShopUrl() . "index.php?cl=order&fnc=return&stoken={$sessionChallengeToken}"
            . "&sDeliveryAddressMD5={$sDeliveryAddressMD5}&adyenPspReference={$pspReference}"
            . "&adyenResultCode={$resultCode}&adyenAmountCurrency={$amountCurrency}";

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    private function createConfigMock($getChopIdInvokeAmount = 1): Config
    {
        $shopConfigMock = $this->createMock(Config::class);
        $shopConfigMock->expects($this->exactly($getChopIdInvokeAmount))
            ->method('getShopId')
            ->willReturn(1);

        return $shopConfigMock;
    }
}
