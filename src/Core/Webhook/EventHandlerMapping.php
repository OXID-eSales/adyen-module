<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CaptureHandler;

class EventHandlerMapping
{
    public const MAPPING = [
        'AUTHORISATION' => AuthorisationHandler::class,
        'CAPTURE' => CaptureHandler::class
    ];
}
