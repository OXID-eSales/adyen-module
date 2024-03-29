<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPICancels;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCancels;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenAPIResponseCancelsTest extends AbstractAdyenAPIResponseTest
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCancels::setCancel
     * @dataProvider getTestData
     */
    public function testSetCancel(array $params, int $errorInvokeAmount, int $paymentsExceptionInvokeAmount, $result)
    {
        $checkoutService = $this->createCheckoutServiceMock($params, 'cancels', $result);

        /** @var AdyenAPICancels $ApiCancels */
        $ApiCancels = $this->createMock(AdyenAPICancels::class);
        $ApiCancels->expects($this->once())
            ->method('getAdyenCancelParams')
            ->willReturn($params);

        $ApiResponseCancels = $this->createAdyenAPIResponse(
            AdyenAPIResponseCancels::class,
            $checkoutService,
            $errorInvokeAmount,
            $paymentsExceptionInvokeAmount,
            'payments not found in Adyen-Response'
        );

        $this->assertEquals($result, $ApiResponseCancels->setCancel($ApiCancels));
    }

    public function getTestData(): array
    {
        return [
            [
                [],
                0,
                0,
                ['success' => true],
            ],
            [
                [],
                1,
                1,
                null,
            ],
        ];
    }
}
