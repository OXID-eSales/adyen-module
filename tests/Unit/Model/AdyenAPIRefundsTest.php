<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIRefunds;

class AdyenAPIRefundsTest extends UnitTestCase
{
    public function testSetGetAdyenRefundsParams(): void
    {
        $model = new AdyenAPIRefunds();
        $model->setCurrencyAmount('1000');
        $model->setCurrencyName('EUR');
        $model->setMerchantAccount('TestMerchant');
        $model->setReference('TestReference');
        $model->setPspReference('TestPSPReference');
        $model->setMerchantApplicationName('TestMerchantApplicationName');
        $model->setMerchantApplicationVersion('Testv1.0.0');
        $model->setPlatformName('TestPlatformName');
        $model->setPlatformVersion('TestPlatformVersion');
        $model->setPlatformIntegrator('TestPlatformVersion');

        $this->assertSame([
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'reference' => 'TestReference',
            'paymentPspReference' => 'TestPSPReference',
            'merchantAccount' => 'TestMerchant',
            'applicationInfo' => [
                'merchantApplication' => [
                    'name' => 'TestMerchantApplicationName',
                    'version' => 'Testv1.0.0'
                ],
                'externalPlatform' => [
                    'name' => 'TestPlatformName',
                    'version' => 'TestPlatformVersion',
                    'integrator' => 'TestPlatformVersion'
                ]
            ]
        ], $model->getAdyenRefundsParams());
    }
}
