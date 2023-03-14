<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Dependency;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use Exception;

/** unit to make sure the adyen module settings are present for codeception e2e tests */
class AdyenModuleSettings
{
    use ServiceContainer;

    public function saveSettingsFromEnv(): void
    {
        $moduleConfigurationBridge = $this->getServiceFromContainer(
            ModuleConfigurationDaoBridgeInterface::class
        );
        $moduleConfiguration = $moduleConfigurationBridge->get('osc_adyen');
        $settingsToBeSaved = [
            ModuleSettings::SANDBOX_API_KEY => $this->getEnvVar('ADYEN_API_KEY'),
            ModuleSettings::SANDBOX_CLIENT_KEY => $this->getEnvVar('ADYEN_CLIENT_KEY'),
            ModuleSettings::SANDBOX_MERCHANT_ACCOUNT => $this->getEnvVar('ADYEN_MERCHANT_ACCOUNT'),
        ];
        $this->injectSettings($moduleConfiguration, $settingsToBeSaved);

        $moduleConfigurationBridge->save($moduleConfiguration);
    }

    /**
     * @throws Exception
     */
    private function getEnvVar(string $envKey): string
    {
        if (empty($_ENV[$envKey])) {
            throw new Exception(
                sprintf(
                    'the env variable %s is not setup, please configure it in the tests/.env'
                    . ', have a look at the .env.example',
                    $envKey
                )
            );
        }

        return $_ENV[$envKey];
    }

    private function injectSettings(ModuleConfiguration $moduleConfiguration, array $settingsToBeSaved): void
    {
        foreach ($settingsToBeSaved as $settingsName => $settingsValue) {
            $moduleConfiguration->getModuleSetting($settingsName)->setValue($settingsValue);
        }
    }
}
