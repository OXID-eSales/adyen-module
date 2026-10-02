<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Service;

use Adyen\Client;
use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 * Pins the Checkout API version independently of adyen/php-api-library.
 *
 * Library 14.x hardcodes Checkout API v70, but co-badged card compliance (IFR)
 * requires v71. Every checkout resource of the library builds its endpoint via
 * getApiCheckoutVersion(), so overriding it here switches all calls at once.
 * v70 -> v71 is additive for all endpoints the module uses.
 */
class AdyenClient extends Client
{
    public function getApiCheckoutVersion()
    {
        return Module::ADYEN_CHECKOUT_API_VERSION;
    }
}
