<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorisationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancellationHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CaptureHandler;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\RefundHandler;

class EventHandlerMapping
{
    public const MAPPING = [
        AuthorisationHandler::AUTHORISATION_EVENT_CODE => AuthorisationHandler::class,
        CaptureHandler::CAPTURE_EVENT_CODE => CaptureHandler::class,
        RefundHandler::REFUND_EVENT_CODE => RefundHandler::class,
        CancellationHandler::CANCEL_EVENT_CODE => CancellationHandler::class,
        CancelRefundHandler::CANCELORREFUND_EVENT_CODE => CancelRefundHandler::class
    ];
}
