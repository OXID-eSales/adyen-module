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
use OxidSolutionCatalysts\Adyen\Core\Module;

class Context extends BasicContext
{
    /** @var Config */
    protected $shopConfig;

    /** @param Config $shopConfig */
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
        $controller = 'AdyenWebhookController&shp=' . $this->getCurrentShopId();
        return $this->getControllerUrl($controller);
    }

    public function getPaymentReturnUrl(
        string $sessionChallengeToken,
        string $sDeliveryAddressMD5,
        string $pspReference,
        string $resultCode,
        string $amountCurrency
    ): string {
        return $this->getControllerUrl(
            'order',
            [
                'fnc' => 'return',
                'stoken' => $sessionChallengeToken,
                'sDeliveryAddressMD5' => $sDeliveryAddressMD5,
                Module::ADYEN_HTMLPARAM_PSPREFERENCE_NAME => $pspReference,
                Module::ADYEN_HTMLPARAM_RESULTCODE_NAME => $resultCode,
                Module::ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME => $amountCurrency,
            ]
        );
    }

    /**
     * Get Url for Controller
     *
     * @param string $controller Name of the controller
     */
    public function getControllerUrl(string $controller, array $optionalGetParameters = null): string
    {
        $url = $this->getShopUrl() . 'index.php?cl=' . $controller;

        if (is_array($optionalGetParameters) && count($optionalGetParameters) > 0) {
            $url .= '&' . http_build_query($optionalGetParameters);
        }

        return html_entity_decode(
            $url
        );
    }

    public function getShopUrl(): string
    {
        $facts = new Facts();
        return rtrim($facts->getShopUrl(), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR;
    }
}
