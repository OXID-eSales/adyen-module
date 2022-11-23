<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;

class AdyenAPICancelsTest extends UnitTestCase
{
    public function testSetGetAdyenCancelParams(): void
    {
        $model = new AdyenAPICancels();
        $model->setMerchantAccount('TestMerchant');
        $model->setPspReference('TestPSPReference');
        $model->setReference('TestReference');

        $this->assertSame([
            'reference' => 'TestReference',
            'merchantAccount' => 'TestMerchant',
            'paymentPspReference' => 'TestPSPReference'
        ], $model->getAdyenCancelParams());
    }
}
