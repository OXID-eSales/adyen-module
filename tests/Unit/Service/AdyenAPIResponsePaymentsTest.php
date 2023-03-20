<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenAPIResponsePaymentsTest extends AbstractAdyenAPIResponseTest
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePayments::getPayments
     * @dataProvider getTestData
     */
    public function testGetPayments(array $params, int $errorInvokeAmount, int $paymentsExceptionInvokeAmount, $result)
    {
        $checkoutService = $this->createCheckoutServiceMock($params, 'payments', $result);

        /** @var AdyenAPIPayments $adyenApiPayments */
        $adyenApiPayments = $this->createAdyenAPIMock(
            $params,
            AdyenAPIPayments::class,
            'getAdyenPaymentsParams'
        );

        $adyenAPIResponsePayments = $this->createAdyenAPIResponse(
            AdyenAPIResponsePayments::class,
            $checkoutService,
            $errorInvokeAmount,
            $paymentsExceptionInvokeAmount,
            'payments not found in Adyen-Response'
        );

        $this->assertEquals($result, $adyenAPIResponsePayments->getPayments($adyenApiPayments));
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
