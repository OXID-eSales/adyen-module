<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Exception;

use Exception;

class WebhookUnknownEventTypeException extends Exception
{
    public static function handlerNotFound($type): self
    {
        return new self(sprintf("An unhandled webhook event was processed: [%s]", $type));
    }
}
