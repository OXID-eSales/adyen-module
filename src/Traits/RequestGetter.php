<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidEsales\EshopCommunity\Core\Request;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;

/**
 * Convenience trait to work with Request-Data
 */
trait RequestGetter
{
    use ServiceContainer;

    public function getStringRequestData(string $key): string
    {
        $request = $this->getServiceFromContainer(OxNewService::class)->oxNew(Request::class);
        /** @var string $value */
        $value = $request->getRequestParameter($key, '');
        return $value;
    }

    public function getFloatRequestData(string $key): float
    {
        $value = $this->getStringRequestData($key);
        return (float)$value;
    }
}
