<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Dependency;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidSolutionCatalysts\Adyen\Tests\Codeception\EnvLoader;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

/** unit to make sure the adyen module settings are present for codeception e2e tests */
class AdyenModuleSettings
{
    use ServiceContainer;

    public function saveSettingsFromEnv(): void
    {
        $envLoader = new EnvLoader();
        $moduleConfigurationBridge = $this->getServiceFromContainer(
            ModuleConfigurationDaoBridgeInterface::class
        );
        $moduleConfiguration = $moduleConfigurationBridge->get('osc_adyen');
        $settingsToBeSaved = [
            ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID => $envLoader->getEnvVar('PAYPAL_MERCHANT_ID'),
            ModuleSettings::SANDBOX_API_KEY => $envLoader->getEnvVar('ADYEN_API_KEY'),
            ModuleSettings::SANDBOX_CLIENT_KEY => $envLoader->getEnvVar('ADYEN_CLIENT_KEY'),
            ModuleSettings::SANDBOX_MERCHANT_ACCOUNT => $envLoader->getEnvVar('ADYEN_MERCHANT_ACCOUNT'),
        ];
        $this->injectSettings($moduleConfiguration, $settingsToBeSaved);

        $moduleConfigurationBridge->save($moduleConfiguration);
    }

    private function injectSettings(ModuleConfiguration $moduleConfiguration, array $settingsToBeSaved): void
    {
        foreach ($settingsToBeSaved as $settingsName => $settingsValue) {
            $moduleConfiguration->getModuleSetting($settingsName)->setValue($settingsValue);
        }
    }
}
