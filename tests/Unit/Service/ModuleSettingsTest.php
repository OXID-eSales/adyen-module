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

    public function testModuleErrorLogging(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        $moduleSettings->saveErrorLogging(true);
        $this->assertTrue($moduleSettings->isErrorLogging());

        $moduleSettings->saveErrorLogging(false);
        $this->assertFalse($moduleSettings->isErrorLogging());
    }

    public function testModuleAPIKey(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $moduleSettings->saveAPIKey('test');
        $this->assertEquals('test', $moduleSettings->getAPIKey());
        $this->assertTrue($moduleSettings->checkHealth());

        $moduleSettings->saveAPIKey('');
        $this->assertEquals('', $moduleSettings->getAPIKey());
        $this->assertFalse($moduleSettings->checkHealth());
    }
}
