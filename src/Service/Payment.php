<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\Client;
use Adyen\Service\Checkout;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidSolutionCatalysts\Adyen\Model\Basket as EshopModelBasket;

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

    private ?string $filterAmount = null;
    /**
     * @var Client
     */
    private Client $client;

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

    public function getSession(): Checkout
    {
        $service = new Checkout($this->client);
        $params = [
            'amount' => [
                'currency' => $this->context->getActiveCurrencyName(),
                'value' => $this->getCurrencyFilterAmount(),
            ],
            'countryCode' => $this->userRepository->getUserCountryIso(),
            'merchantAccount' => $this->moduleSettings->getMerchantAccount(),
            'reference' => Module::ADYEN_ORDER_REFERENCE_ID,
            'returnUrl' => $this->getReturnUrl()
        ];

        return $service->sessions($params);
    }

    /**
     * @link [https://docs.adyen.com/development-resources/currency-codes] [Currency codes and minor units]
     */
    public function setCurrencyFilterAmount(string $filterAmount): void
    {
        $this->filterAmount = $filterAmount;
    }

    /**
     * @link [https://docs.adyen.com/development-resources/currency-codes] [Currency codes and minor units]
     */
    public function getCurrencyFilterAmount(): string
    {
        if (is_null($this->filterAmount)) {
            $currencyDecimals = $this->context->getActiveCurrencyDecimals();
            $this->filterAmount = '10' . str_repeat('0', $currencyDecimals);
        }
        return $this->filterAmount;
    }

    private function getReturnUrl(): string
    {
        return $this->context->getCurrentShopUrl() . 'index.php?cl=order';
    }
}
