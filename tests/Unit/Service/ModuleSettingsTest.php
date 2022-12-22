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
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                ],
                'gettingMethod' => 'checkConfigHealth',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'checkConfigHealth',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => false
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_SANDBOX
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_LIVE
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'sandboxAPIKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'liveAPIKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'sandboxClientKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'liveClientKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'sandboxHmacSignature'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'liveHmacSignature'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'sandboxMerchantAccount'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'liveMerchantAccount'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxPayPalMerchantId', Module::MODULE_ID, 'sandboxPayPalMerchantId'],
                    ['osc_adyen_LivePayPalMerchantId', Module::MODULE_ID, 'livePayPalMerchantId'],
                ],
                'gettingMethod' => 'getPayPalMerchantId',
                'gettingValue' => 'sandboxPayPalMerchantId'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxPayPalMerchantId', Module::MODULE_ID, 'sandboxPayPalMerchantId'],
                    ['osc_adyen_LivePayPalMerchantId', Module::MODULE_ID, 'livePayPalMerchantId'],
                ],
                'gettingMethod' => 'getPayPalMerchantId',
                'gettingValue' => 'livePayPalMerchantId'
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, true],
                ],
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, false],
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
