<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorizationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CaptureHandler;

class EventHandlerMapping
{
    public const MAPPING = [
        AuthorizationHandler::AUTHORIZATION_EVENT_CODE => AuthorizationHandler::class,
        CaptureHandler::CAPTURE_EVENT_CODE => CaptureHandler::class
    ];
}
