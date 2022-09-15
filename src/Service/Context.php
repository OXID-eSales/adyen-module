<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
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
     * @return string
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

    /**
     * @return string
     */
    private function getAdyenLogFileName(): string
    {
        return "adyen_" . date("Y-m-d") . ".log";
    }

    /**
     * @return int
     */
    public function getCurrentShopId(): int
    {
        return $this->shopConfig->getShopId();
    }

    /**
     * @return string
     */
    public function getActiveCurrencyName(): string
    {
        return $this->shopConfig->getActShopCurrencyObject()->name;
    }

    /**
     * @return string
     */
    public function getActiveCurrencySign(): string
    {
        return $this->shopConfig->getActShopCurrencyObject()->sign;
    }
}
