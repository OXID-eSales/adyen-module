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
        $model->setShopperReference('123456');
        $model->setShopperCountryCode('DE');
        $model->setLineItems(['lineItem' => 'test']);
        $model->setOrigin('https://origin.test.de');
        $model->setDeliveryAddress([
            'city' => 'Musterhausen',
            'country' => 'Germany',
            'houseNumberOrName' => '12',
            'postalCode' => '12345',
            'stateOrProvince' => 'TestProvinz',
            'street' => 'Testallee'
        ]);
        $model->setShopperName([
            'firstName' => 'Max',
            'lastName' => 'Muster'
        ]);

        $this->assertSame(
            [
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
                'deliveryAddress' => [
                    'city' => 'Musterhausen',
                    'country' => 'Germany',
                    'houseNumberOrName' => '12',
                    'postalCode' => '12345',
                    'stateOrProvince' => 'TestProvinz',
                    'street' => 'Testallee'
                ],
                'shopperName' => [
                    'firstName' => 'Max',
                    'gender' => 'UNKNOWN',
                    'lastName' => 'Muster'
                ],
                'shopperEmail' => 'test@test.de',
                'shopperIP' => '1.2.3.4',
                'shopperReference' => '123456',
                'countryCode' => 'DE',
                'authenticationData' => [
                    'threeDSRequestData' => [
                        'nativeThreeDS' => 'preferred'
                    ]
                ],
                'channel' => 'Web',
                'origin' => 'https://origin.test.de',
                'lineItems' => ['lineItem' => 'test'],
            ],
            $model->getAdyenPaymentsParams()
        );
    }
}
