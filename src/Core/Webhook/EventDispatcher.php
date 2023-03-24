<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class EventDispatcher
{
    use ServiceContainer;

    /**
     * @param Event $event
     * @throws WebhookEventTypeException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function dispatch(Event $event): void
    {
        $handlers = EventHandlerMapping::MAPPING;
        $eventType = $event->getEventType();

        if (!isset($handlers[$eventType])) {
            throw WebhookEventTypeException::handlerNotFound($eventType);
        }

        $handler = $this->getServiceFromContainer(OxNewService::class)->oxNew($handlers[$eventType]);
        $handler->handle($event);
    }
}
