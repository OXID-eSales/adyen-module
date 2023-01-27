<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Traits;


use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidSolutionCatalysts\Adyen\Core\Module;

trait Setting
{
    /**
     * @param mixed $value
     */
    private function updateModuleSetting(string $name, $value): void
    {
        /** @var ModuleSettingBridgeInterface $moduleSettingsBridge */
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