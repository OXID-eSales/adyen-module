<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;

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
}
