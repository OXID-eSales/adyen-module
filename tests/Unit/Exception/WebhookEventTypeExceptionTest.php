<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Exception;

use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use PHPUnit\Framework\TestCase;

class WebhookEventTypeExceptionTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException::handlerNotFound
     */
    public function testHandlerNotFound(): void
    {
        $type = 'type';
        $expectedException = new WebhookEventTypeException(sprintf("Event handler for '%s' not found.", $type));
        $this->assertEquals($expectedException, WebhookEventTypeException::handlerNotFound($type));
    }
}
