<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Traits;

use JsonException;

/**
 * Convenience trait to work with JSON-Data
 */
trait Json
{
    protected function getJsonPostData(): string
    {
        $result = file_get_contents('php://input');
        return $result ?: '';
    }

    protected function jsonToArray(string $json): array
    {
        try {
            $result = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($result)) {
                throw new JsonException();
            }
        } catch (JsonException $exception) {
            $result = [];
        }
        return $result;
    }

    protected function arrayToJson(array $data): string
    {
        try {
            $result = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
        } catch (JsonException $exception) {
            $result = '';
        }
        return $result;
    }
}
