<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;

class AdyenAPIPaymentsTest extends UnitTestCase
{
    public function testSetGetAdyenSessionParams(): void
    {
        $model = new AdyenAPIPayments();
        $model->setCurrencyAmount('1000');
        $model->setCurrencyName('EUR');
        $model->setMerchantAccount('TestMerchant');
        $model->setReference('TestReference');
        $model->setReturnUrl('ReturnUrl');
        $model->setPaymentMethod(['TestPaymentMethods']);
        $model->setMerchantApplicationName('TestMerchantApplicationName');
        $model->setMerchantApplicationVersion('Testv1.0.0');

        $this->assertSame([
            'paymentMethod' => ['TestPaymentMethods'],
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'reference' => 'TestReference',
            'returnUrl' => 'ReturnUrl',
            'merchantAccount' => 'TestMerchant',
            'applicationInfo' => [
                'merchantApplication' => [
                    'name' => 'TestMerchantApplicationName',
                    'version' => 'Testv1.0.0'
                ]
            ]
        ], $model->getAdyenPaymentsParams());
    }
}
