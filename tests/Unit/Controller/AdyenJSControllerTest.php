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
use OxidSolutionCatalysts\Adyen\Core\Response;

class AdyenJSControllerTest extends UnitTestCase
{
    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testGetAdyenJsonSession()
    {
        $utilsStub = $this->createPartialMock(Utils::class, ['showMessageAndExit']);
        $utilsStub->expects($this->once())
            ->method('showMessageAndExit')
            ->with($this->equalTo("{\n    \"id\": \"test\",\n    \"data\": \"test\"\n}"));

        Registry::set(Utils::class, $utilsStub);

        // set DummyData as Response ...
        $controller = $this->createPartialMock(AdyenJSController::class, ['getAdyenSessionId', 'getAdyenSessionData']);
        $controller->expects($this->any())->method('getAdyenSessionId')->willReturn('test');
        $controller->expects($this->any())->method('getAdyenSessionData')->willReturn('test');

        $controller->getAdyenJsonSession();

        $header = Registry::get(Header::class)->getHeader();
        $this->assertContains("Cache-Control: no-cache\r\n", $header);
        $this->assertContains("Content-Type: application/json\r\n", $header);
        $this->assertContains("Status: 200 OK\r\n", $header);
    }
}