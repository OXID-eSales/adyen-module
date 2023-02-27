<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
use PHPUnit\Framework\TestCase;

class JSAPITemplateCheckoutCreateTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate::getCreateId
     */
    public function testGetCreateId(): void
    {
        $service = new JSAPITemplateCheckoutCreate();

        $this->assertEquals('paypal', $service->getCreateId(Module::PAYMENT_PAYPAL_ID));
        $this->assertEquals('googlepay', $service->getCreateId(Module::PAYMENT_GOOGLE_PAY_ID));
        $this->assertEquals('klarna', $service->getCreateId(Module::PAYMENT_KLARNA_ID));
        $this->assertEquals('no_create_id_found', $service->getCreateId('no_found'));
    }
}
