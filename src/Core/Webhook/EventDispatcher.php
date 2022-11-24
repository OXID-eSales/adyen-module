<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;

class EventDispatcher
{
    /**
     * @param Event $event
     * @throws WebhookEventTypeException
     */
    public function dispatch(Event $event): void
    {
        $handlers = EventHandlerMapping::MAPPING;
        $eventType = $event->getEventType();

        if (!isset($handlers[$eventType])) {
            throw WebhookEventTypeException::handlerNotFound($eventType);
        }

        $handler = oxNew($handlers[$eventType]);
        $handler->handle($event);
    }
}
