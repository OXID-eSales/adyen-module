<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ModuleSettingsTest extends UnitTestCase
{
    use ServiceContainer;

    public function testModuleOperationModeDefault(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $moduleSettings->saveOperationMode('');
        $this->assertEquals(ModuleSettings::OPERATION_MODE_SANDBOX, $moduleSettings->getOperationMode());
        $this->assertTrue($moduleSettings->isSandBoxMode());
    }

    public function testModuleOperationMode(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $moduleSettings->saveOperationMode(ModuleSettings::OPERATION_MODE_SANDBOX);
        $this->assertEquals(ModuleSettings::OPERATION_MODE_SANDBOX, $moduleSettings->getOperationMode());
        $this->assertTrue($moduleSettings->isSandBoxMode());

        $moduleSettings->saveOperationMode(ModuleSettings::OPERATION_MODE_LIVE);
        $this->assertEquals(ModuleSettings::OPERATION_MODE_LIVE, $moduleSettings->getOperationMode());
        $this->assertFalse($moduleSettings->isSandBoxMode());
    }

    public function testModuleOperationModeIncorrectValue(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $moduleSettings->saveOperationMode('some_other_value');
        $this->assertEquals(ModuleSettings::OPERATION_MODE_SANDBOX, $moduleSettings->getOperationMode());
    }

    public function testModuleLoggingActive(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $moduleSettings->saveLoggingActive(true);
        $this->assertTrue($moduleSettings->isLoggingActive());

        $moduleSettings->saveLoggingActive(false);
        $this->assertFalse($moduleSettings->isLoggingActive());
    }

    public function testModuleCredentialsKey(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveAPIKey('testapikey');
        $moduleSettings->saveClientKey('testclientkey');
        $moduleSettings->saveHmacSignature('testhmacsignature');
        $moduleSettings->saveMerchantAccount('testmerchantaccount');
        $moduleSettings->saveNotificationUsername('testnotificationusername');
        $moduleSettings->saveNotificationPassword('testnotificationpassword');
        $this->assertEquals('testapikey', $moduleSettings->getAPIKey());
        $this->assertEquals('testclientkey', $moduleSettings->getClientKey());
        $this->assertEquals('testhmacsignature', $moduleSettings->getHmacSignature());
        $this->assertEquals('testmerchantaccount', $moduleSettings->getMerchantAccount());
        $this->assertEquals('testnotificationusername', $moduleSettings->getNotificationUsername());
        $this->assertEquals('testnotificationpassword', $moduleSettings->getNotificationPassword());
        $this->assertTrue($moduleSettings->checkHealth());

        // some option is missing
        $moduleSettings->saveAPIKey('');
        $moduleSettings->saveClientKey('testclientkey');
        $moduleSettings->saveHmacSignature('');
        $moduleSettings->saveMerchantAccount('');
        $moduleSettings->saveNotificationUsername('');
        $moduleSettings->saveNotificationPassword('');
        $this->assertEquals('', $moduleSettings->getAPIKey());
        $this->assertEquals('testclientkey', $moduleSettings->getClientKey());
        $this->assertEquals('', $moduleSettings->getHmacSignature());
        $this->assertEquals('', $moduleSettings->getMerchantAccount());
        $this->assertEquals('', $moduleSettings->getNotificationUsername());
        $this->assertEquals('', $moduleSettings->getNotificationPassword());
        $this->assertFalse($moduleSettings->checkHealth());

        // all options are missing
        $moduleSettings->saveAPIKey('');
        $moduleSettings->saveClientKey('');
        $moduleSettings->saveHmacSignature('');
        $moduleSettings->saveMerchantAccount('');
        $moduleSettings->saveNotificationUsername('');
        $moduleSettings->saveNotificationPassword('');
        $this->assertEquals('', $moduleSettings->getAPIKey());
        $this->assertEquals('', $moduleSettings->getClientKey());
        $this->assertEquals('', $moduleSettings->getHmacSignature());
        $this->assertEquals('', $moduleSettings->getMerchantAccount());
        $this->assertEquals('', $moduleSettings->getNotificationUsername());
        $this->assertEquals('', $moduleSettings->getNotificationPassword());
        $this->assertFalse($moduleSettings->checkHealth());
    }
}
