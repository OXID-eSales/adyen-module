<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Exception;

use Exception;

class WebhookEventException extends Exception
{
    public static function mandatoryDataNotFound(): self
    {
        return new self('Required data not found in request');
    }

    public static function byOrderId(string $orderOxId): self
    {
        return new self(sprintf("Order with oxorder.oxid '%s' not found", $orderOxId));
    }

    public static function dataNotFound(): self
    {
        return new self('Can not get request data');
    }

    public static function hmacValidationFailed(): self
    {
        return new self('HMAC Signature Validation F');
    }
}
