<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use OxidEsales\EshopCommunity\Core\Request;

/**
 * Convenience trait to work with Request-Data
 */
trait RequestGetter
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getStringRequestData(string $key): string
    {
        $request = oxNew(Request::class);
        /** @var string $value */
        $value = $request->getRequestParameter($key, '');
        return $value;
    }

    protected function getFloatRequestData(string $key): float
    {
        $value = $this->getStringRequestData($key);
        return (float)$value;
    }
}
