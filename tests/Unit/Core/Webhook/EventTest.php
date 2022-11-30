<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook;

use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorizationHandler;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testGetData()
    {
        $arrayData = [
            "live" => "false",
            "notificationItems" => [
                "NotificationRequestItem" => [
                    "pspReference" => "9313547924770610",
                    "eventCode" => "AUTHORIZATION",
                    "merchantAccountCode" => "testMerchantAccount",
                    "merchantReference" => "TestMerchantReference",
                    "success" => "true",
                ]
            ]
        ];

        $event = oxNew(Event::class, $arrayData, AuthorizationHandler::AUTHORIZATION_EVENT_CODE);

        $data = $event->getData();

        $this->assertEquals("false", $data["live"]);
        $this->assertEquals(
            "9313547924770610",
            $data["notificationItems"]["NotificationRequestItem"]["pspReference"]
        );
        $this->assertEquals(
            "AUTHORIZATION",
            $data["notificationItems"]["NotificationRequestItem"]["eventCode"]
        );
        $this->assertEquals(
            "testMerchantAccount",
            $data["notificationItems"]["NotificationRequestItem"]["merchantAccountCode"]
        );
        $this->assertEquals(
            "TestMerchantReference",
            $data["notificationItems"]["NotificationRequestItem"]["merchantReference"]
        );
        $this->assertEquals("true", $data["notificationItems"]["NotificationRequestItem"]["success"]);
    }
}
