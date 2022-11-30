<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Service\Checkout;

/**
 * @extendable-class
 */
class AdyenAPIResponse
{
    /** @var Client */
    protected Client $client;

    /** @var SessionSettings */
    protected SessionSettings $session;

    public function __construct(
        AdyenSDKLoader $adyenSDK,
        SessionSettings $session
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
