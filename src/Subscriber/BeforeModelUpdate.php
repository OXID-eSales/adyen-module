<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

#AfterModelUpdateEvent

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * @extendable-class
 */
class BeforeModelUpdate implements EventSubscriberInterface
{
    use ServiceContainer;

    public function handle(BeforeModelUpdateEvent $event): BeforeModelUpdateEvent
    {
        $event->getModel();

        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeModelUpdateEvent::class => 'handle',
        ];
    }
}
