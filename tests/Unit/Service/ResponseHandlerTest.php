<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Response;
use OxidSolutionCatalysts\Adyen\Service\ResponseHandler;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class ResponseHandlerTest extends UnitTestCase
{
    use ServiceContainer;

    public function testResponse(): void
    {
        $responseStub = $this->createConfiguredMock(Response::class, []);

        $sut = new ResponseHandler($responseStub);
        $this->assertSame($responseStub, $sut->response());
    }
}