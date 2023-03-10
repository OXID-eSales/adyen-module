<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;
use Monolog\Logger;

class AdyenSDKLoader
{
    /** @var ModuleSettings */
    private ModuleSettings $moduleSettings;

    /** @var Logger */
    private Logger $moduleLogger;

    /**
     * @param ModuleSettings $moduleSettings
     * @param Logger $moduleLogger
     */
    public function __construct(
        ModuleSettings $moduleSettings,
        Logger $moduleLogger
    ) {
        $this->moduleSettings = $moduleSettings;
        $this->moduleLogger = $moduleLogger;
    }

    /**
     * @return Client
     * @throws AdyenException
     */
    public function getAdyenSDK(): Client
    {
        $adyenConfig = oxNew(Config::class);
        $adyenConfig->set('x-api-key', $this->moduleSettings->getAPIKey());

        $environment = $this->moduleSettings->isSandBoxMode() ?
            Environment::TEST :
            Environment::LIVE;

        $sdk = oxNew(Client::class, $adyenConfig);
        $sdk->setEnvironment(
            $environment,
            $this->moduleSettings->getEndPointUrlPrefix()
        );
        if ($this->moduleSettings->isLoggingActive()) {
            $sdk->setLogger($this->moduleLogger);
        }
        return $sdk;
    }
}
