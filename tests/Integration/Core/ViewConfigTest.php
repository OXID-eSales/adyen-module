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

final class ViewConfigTest extends UnitTestCase
{
    public function testCheckAdyenHealth(): void
    {
        $this->updateModuleSetting('osc_adyen_OperationMode', ModuleSettings::OPERATION_MODE_SANDBOX);
        $this->updateModuleSetting('osc_adyen_SandboxAPIKey', 'SandboxAPIKey');
        $this->updateModuleSetting('osc_adyen_SandboxClientKey', 'SandboxClientKey');
        $this->updateModuleSetting('osc_adyen_SandboxHmacSignature', 'SandboxHmacSignature');
        $this->updateModuleSetting('osc_adyen_SandboxMerchantAccount', 'SandboxMerchantAccount');
        $this->updateModuleSetting('osc_adyen_SandboxNotificationUsername', 'SandboxNotificationUsername');
        $this->updateModuleSetting('osc_adyen_SandboxNotificationPassword', 'SandboxNotificationPassword');

        $viewConfig = $this->getViewConfig();
        $this->assertTrue($viewConfig->checkAdyenHealth());
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
}
