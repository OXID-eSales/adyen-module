<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Exception;

use OxidSolutionCatalysts\Adyen\Exception\WebhookEventException;
use PHPUnit\Framework\TestCase;

class WebhookEventExceptionTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Exception\WebhookEventException::mandatoryDataNotFound
     */
    public function testMandatoryDataNotFound(): void
    {
        $expectedException = new WebhookEventException('Required data not found in request');
        $this->assertEquals($expectedException, WebhookEventException::mandatoryDataNotFound());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Exception\WebhookEventException::byOrderId
     */
    public function testByOrderId(): void
    {
        $orderOxId = 'orderOxId';
        $expectedException = new WebhookEventException(sprintf("Order with oxorder.oxid '%s' not found", $orderOxId));
        $this->assertEquals($expectedException, WebhookEventException::byOrderId($orderOxId));
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Exception\WebhookEventException::dataNotFound
     */
    public function testDataNotFound(): void
    {
        $expectedException = new WebhookEventException('Can not get request data');
        $this->assertEquals($expectedException, WebhookEventException::dataNotFound());
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Exception\WebhookEventException::hmacValidationFailed
     */
    public function testHmacValidationFailed(): void
    {
        $expectedException = new WebhookEventException('HMAC Signature Validation F');
        $this->assertEquals($expectedException, WebhookEventException::hmacValidationFailed());
    }
}
