<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenAPICaptures;
use OxidSolutionCatalysts\Adyen\Model\AdyenAPIPayments;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponseCaptures;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentDetails;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenAPIResponsePaymentDetailsTest extends AbstractAdyenAPIResponseTest
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentDetails::getPaymentDetails
     * @dataProvider getTestData
     */
    public function testGetPaymentDetails(
        array $params,
        int $errorInvokeAmount,
        int $paymentsExceptionInvokeAmount,
        $result
    ) {
        $checkoutService = $this->createCheckoutServiceMock($params, 'paymentsDetails', $result);

        $adyenAPIResponsePaymentDetails = $this->createAdyenAPIResponse(
            AdyenAPIResponsePaymentDetails::class,
            $checkoutService,
            $errorInvokeAmount,
            $paymentsExceptionInvokeAmount,
            'paymentdetails not found in Adyen-Response'
        );

        $this->assertEquals($result, $adyenAPIResponsePaymentDetails->getPaymentDetails($params));
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
                0,
                null,
            ],
        ];
    }
}
