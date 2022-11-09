<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;
use OxidEsales\Facts\Facts;

class ViewConfig extends ViewConfig_parent
{
    use AdyenAPI;

    /**
     * @var ModuleSettings
     */
    protected $moduleSettings;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
    }

    public function checkAdyenHealth(): bool
    {
        return $this->moduleSettings->checkHealth();
    }

    public function getAdyenOperationMode(): string
    {
        return $this->moduleSettings->getOperationMode();
    }

    public function getAdyenClientKey(): string
    {
        return $this->moduleSettings->getClientKey();
    }

    public function getAdyenSDKVersion(): string
    {
        return Module::ADYEN_SDK_VERSION;
    }

    public function getAdyenIntegrityJS(): string
    {
        return Module::ADYEN_INTEGRITY_JS;
    }

    public function getAdyenIntegrityCSS(): string
    {
        return Module::ADYEN_INTEGRITY_CSS;
    }

    /**
     * Get webhook controller url
     *
     * @return string
     */
    public function getWebhookControllerUrl(): string
    {
        $webhookUrl = 'index.php?cl=AdyenWebhookController&shp=' . Registry::getConfig()->getShopId();
        $facts = new Facts();

        return html_entity_decode(
            $facts->getShopUrl() . $webhookUrl
        );
    }
}
