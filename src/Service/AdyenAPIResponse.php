<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Exception;
use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Service\Checkout;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPaymentMethods;

/**
 * @extendable-class
 */
class AdyenAPIResponse
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var Session
     */
    protected Session $session;

    public function __construct(
        AdyenSDKLoader $adyenSDK,
        Session $session
    ) {
        $this->client = $adyenSDK->getAdyenSDK();
        $this->session = $session;
    }

    /**
     * @return Checkout
     * @throws AdyenException
     */
    protected function createCheckout(): Checkout
    {
        return new Checkout($this->client);
    }
}
