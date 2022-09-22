<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Monolog\Logger;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use PHPUnit\Framework\TestCase;
use Adyen\Client;

class AdyenSDKLoaderTest extends TestCase
{
    /**
     * @dataProvider providerSDKLoadingData
     */
    public function testSDKLoading($moduleSettingsValues, $loggerName): void
    {
        $sut = $this->getSut($moduleSettingsValues);

        $loadedSdk = $sut->getAdyenSDK();
        $this->assertInstanceOf(Client::class, $loadedSdk);
        $this->assertInstanceOf(Logger::class, $loadedSdk->getLogger());
        $this->assertSame($loggerName, $loadedSdk->getLogger()->getName());
    }

    protected function getSut($moduleSettingValues): AdyenSDKLoader
    {
        $moduleSettings = $this->createConfiguredMock(ModuleSettings::class, $moduleSettingValues);
        $loggingHandler = $this->createPartialMock(Logger::class, ['getName']);
        $loggingHandler->method('getName')->willReturn('Adyen Payment Logger');

        return new AdyenSDKLoader($moduleSettings, $loggingHandler);
    }

    public function providerSDKLoadingData(): array
    {
        return [
            [
                [
                    'getAPIKey' => 'dummyKey',
                    'isLoggingActive' => false,
                    'isSandboxMode' => false
                ],
                'adyen-php-api-library'
            ],
            [
                [
                    'getAPIKey' => 'dummyKey',
                    'isLoggingActive' => false,
                    'isSandboxMode' => false
                ],
                'adyen-php-api-library'
            ],
            [
                [
                    'getAPIKey' => 'dummyKey',
                    'isLoggingActive' => true,
                    'isSandboxMode' => false
                ],
                'Adyen Payment Logger'
            ],
            [
                [
                    'getAPIKey' => 'dummyKey',
                    'isLoggingActive' => true,
                    'isSandboxMode' => true
                ],
                'Adyen Payment Logger'
            ]
        ];
    }
}
