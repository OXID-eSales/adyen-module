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

    public function testSaveActivePayments(): void
    {
        $value = [Module::STANDARD_PAYMENT_ID];

        $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
        $bridgeStub->expects($this->once())->method('save')->with(
            ModuleSettings::ACTIVE_PAYMENTS,
            [Module::STANDARD_PAYMENT_ID],
            Module::MODULE_ID
        );

        $sut = new ModuleSettings(
            $bridgeStub
        );
        $sut->saveActivePayments($value);
    }

    public function getGetterDataProvider(): array
    {
        return [
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                ],
                'gettingMethod' => 'checkHealth',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'gettingMethod' => 'checkHealth',
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
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'sandboxNotificationUsername'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'liveNotificationUsername'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'sandboxNotificationPassword'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'liveNotificationPassword'
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
}
