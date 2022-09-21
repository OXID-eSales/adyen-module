<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class ViewConfig extends ViewConfig_parent
{
    use ServiceContainer;

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

    public function checkAdyenHealth() : bool
    {
        return $this->moduleSettings->checkHealth();
    }
}