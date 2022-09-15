<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Monolog\Logger;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use PHPUnit\Framework\TestCase;
use Adyen\Client;

class AdyenSDKLoaderTest extends TestCase
{
    public function testSimpleSDKLoading(): void
    {
        $sut = $this->getSut([
            'getAPIKey' => 'dummyKey',
            'isLoggingActive' => false
        ]);

        $loadedSdk = $sut->getAdyenSDK();
        $this->assertInstanceOf(Client::class, $loadedSdk);
        $this->assertInstanceOf(Logger::class, $loadedSdk->getLogger());
        $this->assertSame('adyen-php-api-library', $loadedSdk->getLogger()->getName());
    }

    public function testLoggingSDKLoading(): void
    {
        $sut = $this->getSut([
            'getAPIKey' => 'dummyKey',
            'isLoggingActive' => true
        ]);

        $loadedSdk = $sut->getAdyenSDK();
        $this->assertSame('Adyen Payment Logger', $loadedSdk->getLogger()->getName());
    }

    protected function getSut($moduleSettingValues): AdyenSDKLoader
    {
        $moduleSettings = $this->createConfiguredMock(ModuleSettings::class, $moduleSettingValues);
        $loggingHandler = $this->createPartialMock(Logger::class, ['getName']);
        $loggingHandler->method('getName')->willReturn('Adyen Payment Logger');

        return new AdyenSDKLoader($moduleSettings, $loggingHandler);
    }
}
