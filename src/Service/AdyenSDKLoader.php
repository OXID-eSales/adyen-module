<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;

class AdyenSDKLoader
{
    /**
     * @var ModuleSettings
     */
    private $moduleSettings;

    /**
     * @var DebugHandler
     */
    private $debugHandler;

    /**
     * @param ModuleSettings $moduleSettings
     * @param DebugHandler $debugHandler
     */
    public function __construct(
        ModuleSettings $moduleSettings,
        DebugHandler $debugHandler
    ) {
        $this->moduleSettings = $moduleSettings;
        $this->debugHandler = $debugHandler;
    }

    /**
     * @return Unzer
     */
    public function getAdyenSDK(): Client
    {
        $adyenConfig = oxNew(Config::class);
        $adyenConfig->set('x-api-key', $this->moduleSettings->getAPIKey());

        $sdk = oxNew(Client::class, $adyenConfig);
        $sdk->setEnvironment(
            $this->moduleSettings->isSandBoxMode() ?
                Environment::TEST :
                Environment::LIVE
        );
        $sdk->setLogger($this->debugHandler);

        return $sdk;
    }
}
