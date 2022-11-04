<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPISession;

class AdyenAPISessionTest extends UnitTestCase
{
    public function testSetGetAdyenSessionParams(): void
    {
        $model = new AdyenAPISession();
        $model->setCountryCode('DE');
        $model->setShopperLocale('de_DE');
        $model->setCurrencyFilterAmount('1000');
        $model->setCurrencyName('EUR');
        $model->setMerchantAccount('TestMerchant');
        $model->setReference('TestReference');
        $model->setReturnUrl('ReturnUrl');

        $this->assertSame([
            'amount' => [
                'currency' => 'EUR',
                'value' => '1000',
            ],
            'countryCode' => 'DE',
            'merchantAccount' => 'TestMerchant',
            'reference' => 'TestReference',
            'returnUrl' => 'ReturnUrl',
            'shopperLocale' => 'de_DE'
        ], $model->getAdyenSessionParams());
    }
}
