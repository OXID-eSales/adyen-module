<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

/**
 * Convenience trait to work with JSON-Data
 */
trait DataGetter
{
    public function getAdyenStringData(string $key): string
    {
        /** @var null|string $value */
        $value = $this->getFieldData($key);
        return $value ?? '';
    }

    public function getAdyenFloatData(string $key): float
    {
        return (float)$this->getAdyenStringData($key);
    }

    public function getAdyenBoolData(string $key): bool
    {
        /** @var null|string $value */
        $value = $this->getFieldData($key);
        return (isset($value) && $value === '1');
    }
}
