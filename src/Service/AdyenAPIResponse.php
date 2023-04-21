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
use Adyen\Service\Modification;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * @extendable-class
 */
class AdyenAPIResponse
{
    /** @var Client */
    protected Client $client;

    /** @var SessionSettings */
    protected SessionSettings $session;
    protected LoggerInterface $logger;

    public function __construct(
        AdyenSDKLoader $adyenSDK,
        SessionSettings $session,
        LoggerInterface $logger
    ) {
        $this->client = $adyenSDK->getAdyenSDK();
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @return Checkout
     * @throws AdyenException
     */
    protected function createCheckout(): Checkout
    {
        return new Checkout($this->client);
    }

    protected function createModification(): Modification
    {
        return new Modification($this->client);
    }

    protected function getPaymentsNotFoundException(): Exception
    {
        return new Exception('payments not found in Adyen-Response');
    }
}
