<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

class JSAPITemplateCheckoutCreateTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate::getCreateId
     */
    public function testGetCreateId(): void
    {
        $klarnaPaymentType = 'klarna';
        $service = new JSAPITemplateCheckoutCreate();

        $this->assertEquals('paypal', $service->getCreateId(Module::PAYMENT_PAYPAL_ID));
        $this->assertEquals('googlepay', $service->getCreateId(Module::PAYMENT_GOOGLE_PAY_ID));
        $this->assertEquals('klarna', $service->getCreateId(Module::PAYMENT_KLARNA_LATER_ID));
        $this->assertEquals('klarna_account', $service->getCreateId(Module::PAYMENT_KLARNA_OVER_TIME_ID));
        $this->assertEquals('klarna_paynow', $service->getCreateId(Module::PAYMENT_KLARNA_IMMEDIATE_ID));
        $this->assertEquals('no_create_id_found', $service->getCreateId('no_found'));
    }
}
