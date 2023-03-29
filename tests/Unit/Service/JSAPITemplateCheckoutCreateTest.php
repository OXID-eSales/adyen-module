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
        $service = new JSAPITemplateCheckoutCreate($this->createModuleSettingsMock($klarnaPaymentType));

        $this->assertEquals('paypal', $service->getCreateId(Module::PAYMENT_PAYPAL_ID));
        $this->assertEquals('googlepay', $service->getCreateId(Module::PAYMENT_GOOGLE_PAY_ID));
        $this->assertEquals($klarnaPaymentType, $service->getCreateId(Module::PAYMENT_KLARNA_ID));
        $this->assertEquals('no_create_id_found', $service->getCreateId('no_found'));
    }

    private function createModuleSettingsMock(string $klarnaPaymentType): ModuleSettings
    {
        $mock = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKlarnaPaymentType'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getKlarnaPaymentType')
            ->willReturn($klarnaPaymentType);

        return $mock;
    }
}
