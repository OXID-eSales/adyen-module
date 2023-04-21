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
    private ModuleSettings $moduleSettings;
    private Logger $moduleLogger;
    private OxNewService $oxNewService;

    /**
     * @param ModuleSettings $moduleSettings
     * @param Logger $moduleLogger
     */
    public function __construct(
        ModuleSettings $moduleSettings,
        Logger $moduleLogger,
        OxNewService $oxNewService
    ) {
        $this->moduleSettings = $moduleSettings;
        $this->moduleLogger = $moduleLogger;
        $this->oxNewService = $oxNewService;
    }

    /**
     * @return Client
     * @throws AdyenException
     */
    public function getAdyenSDK(): Client
    {
        $adyenConfig = $this->oxNewService->oxNew(Config::class);
        $adyenConfig->set('x-api-key', $this->moduleSettings->getAPIKey());

        $environment = $this->moduleSettings->isSandBoxMode() ?
            Environment::TEST :
            Environment::LIVE;

        $sdk = $this->oxNewService->oxNew(Client::class, [$adyenConfig]);
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
