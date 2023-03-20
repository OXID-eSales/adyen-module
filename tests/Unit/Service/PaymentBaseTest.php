<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\PaymentBase;
use PHPUnit\Framework\TestCase;

class PaymentBaseTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\PaymentBase::getPaymentExecutionError
     */
    public function testGetConfiguration(): void
    {
        $error = 'error';
        $paymentBase = new PaymentBase();
        $paymentBase->setPaymentExecutionError($error);
        $this->assertEquals($error, $paymentBase->getPaymentExecutionError());
    }
}
