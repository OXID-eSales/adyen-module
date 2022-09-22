<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\PayPal\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\Order;

class OrderTest extends UnitTestCase
{
    public function testSetPSPReferenceId(): void
    {
        $model = $this->createPartialMock(Order::class, []);

        $model->setAdyenPSPReference('testPSPReference');
        $this->assertSame('testPSPReference', $model->getAdyenPSPReference());
    }
}
