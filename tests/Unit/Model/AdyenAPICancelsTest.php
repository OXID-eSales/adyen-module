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
    public function testSetGetAdyenSessionParams(): void
    {
        $model = new AdyenAPICancels();
        $model->setMerchantAccount('TestMerchant');
        $model->setReference('TestReference');

        $this->assertSame([
            'reference' => 'TestReference',
            'merchantAccount' => 'TestMerchant'
        ], $model->getAdyenCancelParams());
    }
}
