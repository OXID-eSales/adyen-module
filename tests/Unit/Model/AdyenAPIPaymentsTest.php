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
    public function testSetGetAdyenPaymentsParams(): void
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
        $model->setPlatformName('TestPlatformName');
        $model->setPlatformVersion('TestPlatformVersion');
        $model->setPlatformIntegrator('TestPlatformVersion');
        $model->setBrowserInfo(['browserInfo']);
        $model->setShopperEmail('test@test.de');
        $model->setShopperIP('1.2.3.4');
        $model->setOrigin('https://origin.test.de');

        $this->assertSame([
            'paymentMethod' => ['TestPaymentMethods'],
            'browserInfo' => ['browserInfo'],
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
                ],
                'externalPlatform' => [
                    'name' => 'TestPlatformName',
                    'version' => 'TestPlatformVersion',
                    'integrator' => 'TestPlatformVersion'
                ]
            ],
            'shopperEmail' => 'test@test.de',
            'shopperIP' => '1.2.3.4',
            'authenticationData' => [
                'threeDSRequestData' => [
                    'nativeThreeDS' => 'preferred'
                ]
            ],
            'channel' => 'Web',
            'origin' => 'https://origin.test.de'
        ], $model->getAdyenPaymentsParams());
    }
}
