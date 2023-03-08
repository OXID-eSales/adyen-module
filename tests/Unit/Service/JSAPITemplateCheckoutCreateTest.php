<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\JSAPITemplateCheckoutCreate;
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
        $service = $this->getServiceFromContainer(JSAPITemplateCheckoutCreate::class);

        $this->assertEquals('paypal', $service->getCreateId(Module::PAYMENT_PAYPAL_ID));
        $this->assertEquals('googlepay', $service->getCreateId(Module::PAYMENT_GOOGLE_PAY_ID));
        $this->assertEquals('klarna', $service->getCreateId(Module::PAYMENT_KLARNA_ID));
        $this->assertEquals('no_create_id_found', $service->getCreateId('no_found'));
    }
}
