<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testGetter()
    {
        $event = oxNew(Event::class, $this->proceedNotificationData());

        $this->assertEquals(false, $event->isLive());
        $this->assertEquals('9313547924770610', $event->getPspReference());
        $this->assertEquals('1233547924770610', $event->getParentPspReference());
        $this->assertEquals('AUTHORIZATION', $event->getEventType());
        $this->assertEquals('TestMerchantReference', $event->getMerchantReference());
        $this->assertEquals(true, $event->isSuccess());
    }

    private function proceedNotificationData()
    {
        return [
            "live" => "false",
            "notificationItems" => [
                [
                    "NotificationRequestItem" => [
                        "additionalData" => [
                            "hmacSignature" => 'dummyHmac',
                        ],
                        "amount" => [
                            "currency" => "EUR",
                            "value" => 1000
                        ],
                        "eventDate" => "2021-01-01T01:00:00+01:00",
                        "pspReference" => "9313547924770610",
                        "originalReference" => "1233547924770610",
                        "eventCode" => "AUTHORIZATION",
                        "merchantReference" => "TestMerchantReference",
                        "success" => "true"
                    ]
                ]
            ]
        ];
    }
}
