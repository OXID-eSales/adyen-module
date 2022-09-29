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
     * @dataProvider getSetterGetterDataProvider
     */
    public function testGetter($values, $settingMethod, $settingValue, $gettingMethod, $gettingValue): void
    {
        $sut = new ModuleSettings(
            $this->getBridgeStub($values)
        );
        $this->assertSame($gettingValue, $sut->$gettingMethod());
    }

    /**
     * @dataProvider getSetterGetterDataProvider
     */
    public function testSetter($values, $settingMethod, $settingValue, $gettingMethod, $gettingValue): void
    {
        $sut = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods([$settingMethod, $gettingMethod])
            ->getMock();

        $sut->method($gettingMethod)->willReturn($gettingValue);
        $sut->expects($this->once())->method($settingMethod);

        $sut->$settingMethod($settingValue);
        $this->assertSame($gettingValue, $sut->$gettingMethod());
    }

    public function getSetterGetterDataProvider(): array
    {
        return [
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'settingMethod' => 'saveOperationMode',
                'settingValue' => ModuleSettings::OPERATION_MODE_SANDBOX,
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'settingMethod' => 'saveOperationMode',
                'settingValue' => ModuleSettings::OPERATION_MODE_LIVE,
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => false
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'settingMethod' => 'saveOperationMode',
                'settingValue' => ModuleSettings::OPERATION_MODE_SANDBOX,
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_SANDBOX
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'settingMethod' => 'saveOperationMode',
                'settingValue' => ModuleSettings::OPERATION_MODE_LIVE,
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_LIVE
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'settingMethod' => 'saveAPIKey',
                'settingValue' => 'sandboxAPIKey',
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'sandboxAPIKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'settingMethod' => 'saveAPIKey',
                'settingValue' => 'liveAPIKey',
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'liveAPIKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'settingMethod' => 'saveClientKey',
                'settingValue' => 'sandboxClientKey',
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'sandboxClientKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'settingMethod' => 'saveClientKey',
                'settingValue' => 'liveClientKey',
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'liveClientKey'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'settingMethod' => 'saveHmacSignature',
                'settingValue' => 'sandboxHmacSignature',
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'sandboxHmacSignature'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'settingMethod' => 'saveHmacSignature',
                'settingValue' => 'liveHmacSignature',
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'liveHmacSignature'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'settingMethod' => 'saveMerchantAccount',
                'settingValue' => 'sandboxMerchantAccount',
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'sandboxMerchantAccount'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'settingMethod' => 'saveMerchantAccount',
                'settingValue' => 'liveMerchantAccount',
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'liveMerchantAccount'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'settingMethod' => 'saveNotificationUsername',
                'settingValue' => 'sandboxNotificationUsername',
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'sandboxNotificationUsername'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'settingMethod' => 'saveNotificationUsername',
                'settingValue' => 'liveNotificationUsername',
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'liveNotificationUsername'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'settingMethod' => 'saveNotificationPassword',
                'settingValue' => 'sandboxNotificationPassword',
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'sandboxNotificationPassword'
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'settingMethod' => 'saveNotificationPassword',
                'settingValue' => 'liveNotificationPassword',
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'liveNotificationPassword'
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, true],
                ],
                'settingMethod' => 'saveLoggingActive',
                'settingValue' => true,
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => true
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, false],
                ],
                'settingMethod' => 'saveLoggingActive',
                'settingValue' => false,
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => false
            ]

        ];
    }

    private function getBridgeStub($valueMap = []): ModuleSettingBridgeInterface
    {
        $bridgeStub = $this->getMockBuilder(ModuleSettingBridgeInterface::class)
            ->onlyMethods(['save', 'get'])
            ->getMock();
        $bridgeStub->method('get')->willReturnMap($valueMap);

        return $bridgeStub;
    }
}
