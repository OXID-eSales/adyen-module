<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidEsales\Eshop\Core\ViewConfig as eShopViewConfig;
use OxidSolutionCatalysts\Adyen\Tests\Integration\Traits\Setting;

final class ViewConfigTest extends UnitTestCase
{
    use Setting;

    public function testcheckAdyenConfigHealth(): void
    {
        $this->updateModuleSetting('osc_adyen_OperationMode', ModuleSettings::OPERATION_MODE_SANDBOX);
        $this->updateModuleSetting('osc_adyen_SandboxAPIKey', 'SandboxAPIKey');
        $this->updateModuleSetting('osc_adyen_SandboxClientKey', 'SandboxClientKey');
        $this->updateModuleSetting('osc_adyen_SandboxHmacSignature', 'SandboxHmacSignature');
        $this->updateModuleSetting('osc_adyen_SandboxMerchantAccount', 'SandboxMerchantAccount');

        $viewConfig = $this->getViewConfig();
        $this->assertTrue($viewConfig->checkAdyenConfigHealth());
    }

    public function testGetAdyenOperationMode(): void
    {
        $viewConfig = $this->getViewConfig();

        $this->updateModuleSetting('osc_adyen_OperationMode', ModuleSettings::OPERATION_MODE_SANDBOX);
        $this->assertSame($viewConfig->getAdyenOperationMode(), ModuleSettings::OPERATION_MODE_SANDBOX);

        // todo: This test does not work if the credentials are set via var/configuration/environment/1.yaml
        //      $this->updateModuleSetting('osc_adyen_OperationMode', ModuleSettings::OPERATION_MODE_LIVE);
        //      $this->assertSame($viewConfig->getAdyenOperationMode(), ModuleSettings::OPERATION_MODE_LIVE);
    }

    public function testIsAdyenLoggingActive(): void
    {
        $viewConfig = $this->getViewConfig();

        $this->updateModuleSetting('osc_adyen_LoggingActive', true);
        $this->assertSame($viewConfig->isAdyenLoggingActive(), true);
        // todo: This test does not work if the credentials are set via var/configuration/environment/1.yaml
        //      $this->updateModuleSetting('osc_adyen_LoggingActive', false);
        //      $this->assertSame($viewConfig->isAdyenLoggingActive(), false);
    }

    public function testGetAdyenClientKey(): void
    {
        $viewConfig = $this->getViewConfig();

        $this->assertSame($viewConfig->getAdyenClientKey(), $this->getModuleSetting('osc_adyen_SandboxClientKey'));
    }

    private function getViewConfig(): ViewConfig
    {
        return Registry::get(eShopViewConfig::class);
    }

    /**
     * @param mixed $value
     */
    private function updateModuleSetting(string $name, $value): void
    {
        $moduleSettingsBridge = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettingBridgeInterface::class);
        $moduleSettingsBridge->save($name, $value, Module::MODULE_ID);
    }

    /**
     * @param mixed $value
     */
    private function getModuleSetting(string $name): string
    {
        $moduleSettingsBridge = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettingBridgeInterface::class);
        return $moduleSettingsBridge->get($name, Module::MODULE_ID);
    }
}
