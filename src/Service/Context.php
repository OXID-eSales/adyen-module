<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

class Context extends BasicContext
{
    /** @var Config */
    protected $shopConfig;

    /**
     * @param Config $shopConfig
     */
    public function __construct(Config $shopConfig)
    {
        $this->shopConfig = $shopConfig;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getAdyenLogFilePath(): string
    {
        return Path::join([
            $this->shopConfig->getLogsDir(),
            'adyen',
            $this->getAdyenLogFileName()
        ]);
    }

    private function getAdyenLogFileName(): string
    {
        return "adyen_" . date("Y-m-d") . ".log";
    }

    public function getCurrentShopId(): int
    {
        return $this->shopConfig->getShopId();
    }

    public function getActiveCurrencyName(): string
    {
        return $this->shopConfig->getActShopCurrencyObject()->name;
    }

    public function getActiveCurrencyDecimals(): int
    {
        return (int) $this->shopConfig->getActShopCurrencyObject()->decimal;
    }

    public function getCurrentShopUrl(): string
    {
        return html_entity_decode(
            $this->shopConfig->getCurrentShopUrl(false)
        );
    }

    public function getActiveCurrencySign(): string
    {
        return $this->shopConfig->getActShopCurrencyObject()->sign;
    }

    public function getWebhookControllerUrl(): string
    {
        return $this->getControllerUrl('AdyenWebhookController');
    }

    public function getPaymentReturnUrl(): string
    {
        return $this->getControllerUrl('thankyou');
    }

    /**
     * Get Url for Controller
     *
     * @param string $controller Name of the controller
     */
    public function getControllerUrl(string $controller): string
    {
        $url = 'index.php?cl=' . $controller;
        $facts = new Facts();

        return html_entity_decode(
            $facts->getShopUrl() . $url
        );
    }
}
