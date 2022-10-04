<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\modules\osc\adyen\src\Service;

use Adyen\Client;
use Adyen\Service\Checkout;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidEsales\Eshop\Application\Model\Basket as EshopModelBasket;

/**
 * @extendable-class
 */
class Payment
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var ModuleSettings
     */
    private ModuleSettings $moduleSettings;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param ModuleSettings $shopConfig
     */
    public function __construct(
        Context $context,
        ModuleSettings $moduleSettings,
        UserRepository $userRepository,
        AdyenSDKLoader $adyenSDK
    ) {
        $this->context = $context;
        $this->moduleSettings = $moduleSettings;
        $this->userRepository = $userRepository;
        $this->client = $adyenSDK->getAdyenSDK();
    }

    public function getSession(
        EshopModelBasket $basket
    ) {
        $service = new Checkout($this->client);
        $params = [
            'amount' => [
                'currency' => $this->context->getActiveCurrencyName(),
                'value' => $this->basket->getAdyenPaymentFilterAmount(),
            ],
            'countryCode' => $this->userRepository->getUserCountryIso(),
            'merchantAccount' => $this->moduleSettings->getMerchantAccount(),
            'reference' => Module::ADYEN_ORDER_REFERENCE_ID,
            'returnUrl' => $this->getReturnUrl()
        ];

        return $service->sessions($params);
    }

    private function getReturnUrl()
    {
        return $this->context->getCurrentShopUrl() . 'index.php?cl=order';
    }
}
