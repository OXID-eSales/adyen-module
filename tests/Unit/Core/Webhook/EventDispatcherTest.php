<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\EventDispatcher;
use OxidSolutionCatalysts\Adyen\Core\Webhook\EventHandlerMapping;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CaptureHandler;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\EventDispatcher::dispatch
     */
    public function testDispatch()
    {
        $eventType = CaptureHandler::CAPTURE_EVENT_CODE;
        $eventMock = $this->createEvent($eventType);
        $dispatcherMock = $this->createDispatcherMock($eventType, $eventMock);

        $dispatcherMock->dispatch($eventMock);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\EventDispatcher::dispatch
     */
    public function testDispatchException()
    {
        $eventType = '';
        $eventMock = $this->createEvent($eventType);

        $dispatcherMock = $this->createDispatcherMock($eventType, $eventMock);

        $this->expectExceptionMessage("Event handler for '' not found.");

        $dispatcherMock->dispatch($eventMock);
    }

    private function createEvent(string $eventType): Event
    {

        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEventType'])
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getEventType')
            ->willReturn($eventType);

        return $eventMock;
    }
    private function createDispatcherMock(string $eventType, Event $eventMock): EventDispatcher
    {
        if (empty($eventType)) {
            return new EventDispatcher();
        }
        $handlerMock = $this->getMockBuilder(EventHandlerMapping::MAPPING[$eventType])
            ->disableOriginalConstructor()
            ->onlyMethods(['handle'])
            ->getMock();
        $handlerMock->expects($this->once())
            ->method('handle')
            ->with($eventMock);

        $oxNewServiceMock = $this->getMockBuilder(OxNewService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['oxNew'])
            ->getMock();
        $oxNewServiceMock->expects($this->once())
            ->method('oxNew')
            ->with(EventHandlerMapping::MAPPING[$eventType])
            ->willReturn($handlerMock);

        $dispatcherMock = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServiceFromContainer'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('getServiceFromContainer')
            ->with(OxNewService::class)
            ->willReturn($oxNewServiceMock);

        return $dispatcherMock;
    }
}
