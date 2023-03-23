<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenAPIResponseCapturesTest extends AbstractAdyenAPIResponseTest
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures::setCapture
     * @dataProvider getTestData
     */
    public function testSetCapture(array $params, int $errorInvokeAmount, int $paymentsExceptionInvokeAmount, $result)
    {
        $checkoutService = $this->createCheckoutServiceMock($params, 'captures', $result);

        /** @var AdyenAPICaptures $adyenApiCaptures */
        $adyenApiCaptures = $this->createMock(AdyenAPICaptures::class);
        $adyenApiCaptures->expects($this->once())
            ->method('getAdyenCapturesParams')
            ->willReturn($params);

        $adyenAPIResponseCaptures = $this->createAdyenAPIResponse(
            AdyenAPIResponseCaptures::class,
            $checkoutService,
            $errorInvokeAmount,
            $paymentsExceptionInvokeAmount,
            'payments not found in Adyen-Response'
        );

        $this->assertEquals($result, $adyenAPIResponseCaptures->setCapture($adyenApiCaptures));
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
