<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller;

use OxidEsales\Eshop\Core\Header;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Controller\AdyenJSController;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseSession;

class AdyenJSControllerTest extends UnitTestCase
{
    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testGetAdyenJsonSession(): void
    {
        $utilsStub = $this->createPartialMock(Utils::class, ['showMessageAndExit']);
        $utilsStub->expects($this->once())
            ->method('showMessageAndExit')
            ->with($this->equalTo("{\n    \"id\": \"test\",\n    \"data\": \"test\"\n}"));

        Registry::set(Utils::class, $utilsStub);

        // set DummyData as Response ...
        $controller = $this->createPartialMock(
            AdyenJSController::class,
            ['getAdyenSessionId', 'getAdyenSessionData']
        );
        $controller->expects($this->any())->method('getAdyenSessionId')->willReturn('test');
        $controller->expects($this->any())->method('getAdyenSessionData')->willReturn('test');

        $controller->getAdyenJsonSession();

        $header = Registry::get(Header::class)->getHeader();
        $this->assertContains("Cache-Control: no-cache\r\n", $header);
        $this->assertContains("Content-Type: application/json\r\n", $header);
        $this->assertContains("Status: 200 OK\r\n", $header);
    }

    public function testGetAdyenSessionIdAndData(): void
    {
        $paymentStub = $this->createPartialMock(
            AdyenAPIResponseSession::class,
            ['getAdyenSessionId', 'getAdyenSessionData']
        );
        $paymentStub->method('getAdyenSessionId')->willReturn('test1');
        $paymentStub->method('getAdyenSessionData')->willReturn('test2');

        $controller = $this->createPartialMock(AdyenJSController::class, ['getAdyenSessionResponse']);
        $controller->method('getAdyenSessionResponse')->willReturn($paymentStub);

        $this->assertSame('test1', $controller->getAdyenSessionId());
        $this->assertSame('test2', $controller->getAdyenSessionData());
    }

    public function testGetAdyenSessionResponse(): void
    {
        $controller = new AdyenJSController();
        $this->assertInstanceOf(AdyenAPIResponseSession::class, $controller->getAdyenSessionResponse());
    }
}
