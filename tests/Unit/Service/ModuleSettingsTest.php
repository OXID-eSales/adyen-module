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
        $gettingValue,
        string $getterOption
    ): void {

        $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
        $bridgeStub->method('get')->willReturnMap($values);

        $sut = new ModuleSettings(
            $bridgeStub
        );
        $assertResult = $getterOption ?
            $sut->$gettingMethod($getterOption) :
            $sut->$gettingMethod();
        $this->assertSame($gettingValue, $assertResult);
    }

    public function testSaveActivePayments(): void
    {
        $value = [Module::PAYMENT_CREDITCARD_ID];

        $bridgeStub = $this->createPartialMock(ModuleSettingBridgeInterface::class, ['save', 'get']);
        $bridgeStub->expects($this->once())->method('save')->with(
            ModuleSettings::ACTIVE_PAYMENTS,
            [Module::PAYMENT_CREDITCARD_ID],
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
                'gettingValue' => true,
                'getterOption' => ''
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
                'gettingValue' => true,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => true,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'isSandBoxMode',
                'gettingValue' => false,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_SANDBOX,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                ],
                'gettingMethod' => 'getOperationMode',
                'gettingValue' => ModuleSettings::OPERATION_MODE_LIVE,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'sandboxAPIKey',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxAPIKey', Module::MODULE_ID, 'sandboxAPIKey'],
                    ['osc_adyen_LiveAPIKey', Module::MODULE_ID, 'liveAPIKey'],
                ],
                'gettingMethod' => 'getAPIKey',
                'gettingValue' => 'liveAPIKey',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'sandboxClientKey',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxClientKey', Module::MODULE_ID, 'sandboxClientKey'],
                    ['osc_adyen_LiveClientKey', Module::MODULE_ID, 'liveClientKey'],
                ],
                'gettingMethod' => 'getClientKey',
                'gettingValue' => 'liveClientKey',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'sandboxHmacSignature',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxHmacSignature', Module::MODULE_ID, 'sandboxHmacSignature'],
                    ['osc_adyen_LiveHmacSignature', Module::MODULE_ID, 'liveHmacSignature'],
                ],
                'gettingMethod' => 'getHmacSignature',
                'gettingValue' => 'liveHmacSignature',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'sandboxMerchantAccount',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxMerchantAccount', Module::MODULE_ID, 'sandboxMerchantAccount'],
                    ['osc_adyen_LiveMerchantAccount', Module::MODULE_ID, 'liveMerchantAccount'],
                ],
                'gettingMethod' => 'getMerchantAccount',
                'gettingValue' => 'liveMerchantAccount',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'sandboxNotificationUsername',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationUsername', Module::MODULE_ID, 'sandboxNotificationUsername'],
                    ['osc_adyen_LiveNotificationUsername', Module::MODULE_ID, 'liveNotificationUsername'],
                ],
                'gettingMethod' => 'getNotificationUsername',
                'gettingValue' => 'liveNotificationUsername',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_SANDBOX],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'sandboxNotificationPassword',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_OperationMode', Module::MODULE_ID, ModuleSettings::OPERATION_MODE_LIVE],
                    ['osc_adyen_SandboxNotificationPassword', Module::MODULE_ID, 'sandboxNotificationPassword'],
                    ['osc_adyen_LiveNotificationPassword', Module::MODULE_ID, 'liveNotificationPassword'],
                ],
                'gettingMethod' => 'getNotificationPassword',
                'gettingValue' => 'liveNotificationPassword',
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, true],
                ],
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => true,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_LoggingActive', Module::MODULE_ID, false],
                ],
                'gettingMethod' => 'isLoggingActive',
                'gettingValue' => false,
                'getterOption' => ''
            ],
            [
                'values' => [
                    ['osc_adyen_SeperateCapture_' . Module::PAYMENT_CREDITCARD_ID, Module::MODULE_ID, true],
                ],
                'gettingMethod' => 'isSeperateCapture',
                'gettingValue' => true,
                'getterOption' => Module::PAYMENT_CREDITCARD_ID
            ],
            [
                'values' => [
                    ['osc_adyen_SeperateCapture_' . Module::PAYMENT_CREDITCARD_ID, Module::MODULE_ID, false],
                ],
                'gettingMethod' => 'isSeperateCapture',
                'gettingValue' => false,
                'getterOption' => Module::PAYMENT_CREDITCARD_ID
            ],
            [
                'values' => [
                    ['osc_adyen_SeperateCapture_' . Module::PAYMENT_PAYPAL_ID, Module::MODULE_ID, true],
                ],
                'gettingMethod' => 'isSeperateCapture',
                'gettingValue' => true,
                'getterOption' => Module::PAYMENT_PAYPAL_ID
            ],
            [
                'values' => [
                    ['osc_adyen_SeperateCapture_' . Module::PAYMENT_PAYPAL_ID, Module::MODULE_ID, false],
                ],
                'gettingMethod' => 'isSeperateCapture',
                'gettingValue' => false,
                'getterOption' => Module::PAYMENT_PAYPAL_ID
            ]
        ];
    }
}
