<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ModuleSettingsTest extends UnitTestCase
{
    use ServiceContainer;

    /**
     * @dataProvider getGetterDataProvider
     */
    public function testGetter(
        array $values,
        string $gettingMethod,
        $gettingValue
    ): void {

        $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
        $bridgeStub->method('get')->willReturnMap($values);

        $sut = new ModuleSettings(
            $bridgeStub
        );

        $this->assertSame($gettingValue, $sut->$gettingMethod());
    }

    /**
     * @dataProvider getCaptureDataProvider
     */
    public function testIsManualCapture(
        string $varName,
        string $paymentId
    ): void {
        $gettingValues = [
            Module::ADYEN_CAPTURE_DELAY_MANUAL => true,
            Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => false,
            Module::ADYEN_CAPTURE_DELAY_DAYS => false
        ];

        foreach ($gettingValues as $gettingKey => $gettingValue) {
            $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
            $bridgeStub->method('get')->willReturnMap([[$varName, Module::MODULE_ID, $gettingKey]]);
            $sut = new ModuleSettings(
                $bridgeStub
            );
            $this->assertSame($gettingValue, $sut->isManualCapture($paymentId));
        }
    }

    public function testSaveActivePayments(): void
    {
        $values = array_keys(Module::PAYMENT_DEFINTIONS);

        $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
        $bridgeStub->expects($this->once())->method('save')->with(
            ModuleSettings::ACTIVE_PAYMENTS,
            $values,
            Module::MODULE_ID
        );

        $sut = new ModuleSettings(
            $bridgeStub
        );
        $sut->saveActivePayments($values);
    }

    public function getGetterDataProvider(): array
    {
        return [
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_API_KEY, Module::MODULE_ID, 'sandboxAPIKey'],
                    [ModuleSettings::SANDBOX_CLIENT_KEY, Module::MODULE_ID, 'sandboxClientKey'],
                    [ModuleSettings::SANDBOX_MERCHANT_ACCOUNT, Module::MODULE_ID, 'sandboxMerchantAccount'],
                ],
                'gettingMethod' => 'checkConfigHealth',
                'gettingValue' => true
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::LIVE_API_KEY, Module::MODULE_ID, 'liveAPIKey'],
                    [ModuleSettings::LIVE_CLIENT_KEY, Module::MODULE_ID, 'liveClientKey'],
                    [ModuleSettings::LIVE_MERCHANT_ACCOUNT, Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'checkConfigHealth',
                'gettingValue' => true
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => true
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => false
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_SANDBOX
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_LIVE
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_API_KEY, Module::MODULE_ID, 'sandboxAPIKey'],
                    [ModuleSettings::LIVE_API_KEY, Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'sandboxAPIKey'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::SANDBOX_API_KEY, Module::MODULE_ID, 'sandboxAPIKey'],
                    [ModuleSettings::LIVE_API_KEY, Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'liveAPIKey'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_CLIENT_KEY, Module::MODULE_ID, 'sandboxClientKey'],
                    [ModuleSettings::LIVE_CLIENT_KEY, Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'sandboxClientKey'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::SANDBOX_CLIENT_KEY, Module::MODULE_ID, 'sandboxClientKey'],
                    [ModuleSettings::LIVE_CLIENT_KEY, Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'liveClientKey'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_HMAC_SIGNATURE, Module::MODULE_ID, 'sandboxHmacSignature'],
                    [ModuleSettings::LIVE_HMAC_SIGNATURE, Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'sandboxHmacSignature'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::SANDBOX_HMAC_SIGNATURE, Module::MODULE_ID, 'sandboxHmacSignature'],
                    [ModuleSettings::LIVE_HMAC_SIGNATURE, Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'liveHmacSignature'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_MERCHANT_ACCOUNT, Module::MODULE_ID, 'sandboxMerchantAccount'],
                    [ModuleSettings::LIVE_MERCHANT_ACCOUNT, Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'sandboxMerchantAccount'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::SANDBOX_MERCHANT_ACCOUNT, Module::MODULE_ID, 'sandboxMerchantAccount'],
                    [ModuleSettings::LIVE_MERCHANT_ACCOUNT, Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'liveMerchantAccount'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    [ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID, Module::MODULE_ID, 'sandboxPayPalMerchantId'],
                    [ModuleSettings::LIVE_PAYPAL_MERCHANT_ID, Module::MODULE_ID, 'livePayPalMerchantId'],
                ],
                'gettingMethod' => 'getPayPalMerchantId',
                'gettingValue' => 'sandboxPayPalMerchantId'
            ],
            [
                'values' => [
                    [ModuleSettings::OPERATION_MODE, Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    [ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID, Module::MODULE_ID, 'sandboxPayPalMerchantId'],
                    [ModuleSettings::LIVE_PAYPAL_MERCHANT_ID, Module::MODULE_ID, 'livePayPalMerchantId'],
                ],
                'gettingMethod' => 'getPayPalMerchantId',
                'gettingValue' => 'livePayPalMerchantId'
            ],
            [
                'values' => [
                    [ModuleSettings::LOGGING_ACTIVE, Module::MODULE_ID, true],
                ],
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => true
            ],
            [
                'values' => [
                    [ModuleSettings::LOGGING_ACTIVE, Module::MODULE_ID, false],
                ],
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => false
            ]
        ];
    }

    public function getCaptureDataProvider(): array
    {
        $captureData = [];

        foreach (Module::PAYMENT_DEFINTIONS as $paymentId => $paymentDef) {
            if ($paymentDef['capturedelay']) {
                $captureData[] = [
                    'varName' => ModuleSettings::CAPTURE_DELAY . $paymentId,
                    'paymentId' => $paymentId
                ];
            }
        }
        return $captureData;
    }
}
