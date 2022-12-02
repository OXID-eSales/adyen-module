<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;
use OxidEsales\Facts\Facts;

class ViewConfig extends ViewConfig_parent
{
    use AdyenAPI;

    protected ModuleSettings $moduleSettings;
    protected Context $context;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $this->context = $this->getServiceFromContainer(Context::class);
    }

    public function checkAdyenHealth(): bool
    {
        return (
            $this->moduleSettings->checkConfigHealth() &&
            $this->existsAdyenPaymentMethods()
        );
    }

    public function checkAdyenConfigHealth(): bool
    {
        return $this->moduleSettings->checkConfigHealth();
    }

    public function getAdyenOperationMode(): string
    {
        return $this->moduleSettings->getOperationMode();
    }

    public function isAdyenLoggingActive(): bool
    {
        return $this->moduleSettings->isLoggingActive();
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

    public function getAdyenHtmlParamStateName(): string
    {
        return Module::ADYEN_HTMLPARAM_PAYMENTSTATEDATA_NAME;
    }

    public function getWebhookControllerUrl(): string
    {
        return $this->context->getWebhookControllerUrl();
    }
}
