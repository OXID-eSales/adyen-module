<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPIRefunds;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseRefunds;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenAPIResponseRefundsTest extends AbstractAdyenAPIResponseTest
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseRefunds::setRefund
     * @dataProvider getTestData
     */
    public function testSetRefund($params, int $errorInvokeAmount, int $paymentsExceptionInvokeAmount, $result)
    {
        $checkoutService = $this->createCheckoutServiceMock($params, 'refunds', $result);

        /** @var AdyenAPIRefunds $ApiRefunds */
        $ApiRefunds = $this->createMock(AdyenAPIRefunds::class);
        $ApiRefunds->expects($this->once())
            ->method('getAdyenRefundsParams')
            ->willReturn($params);

        $ApiRefundsResponse = $this->createAdyenAPIResponse(
            AdyenAPIResponseRefunds::class,
            $checkoutService,
            $errorInvokeAmount,
            $paymentsExceptionInvokeAmount,
            'payments not found in Adyen-Response',
        );

        $this->assertEquals($result, $ApiRefundsResponse->setRefund($ApiRefunds));
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
                false,
            ],
        ];
    }
}
