<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;

class AdyenAPIResponsePaymentMethodsGetGoogleConfigTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentMethods::getGooglePayConfiguration
     */
    public function test()
    {
        $merchantAccount = 'merchantAccount';
        $googleMerchantAccount = 'googleMerchantAccount';
        $moduleService = $this->getMockBuilder(ModuleSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMerchantAccount', 'getGooglePayMerchantId'])
            ->getMock();
        $moduleService->expects($this->once())
            ->method('getMerchantAccount')
            ->willReturn($merchantAccount);
        $moduleService->expects($this->once())
            ->method('getGooglePayMerchantId')
            ->willReturn($googleMerchantAccount);

        $service = $this->getMockBuilder(AdyenAPIResponsePaymentMethods::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServiceFromContainer'])
            ->getMock();
        $service->expects($this->once())
            ->method('getServiceFromContainer')
            ->with(ModuleSettings::class)
            ->willReturn($moduleService);

        $this->assertEquals(
            [
                'gatewayMerchantId' => $merchantAccount,
                'merchantId' => $googleMerchantAccount,
            ],
            $service->getGooglePayConfiguration()
        );
    }
}
