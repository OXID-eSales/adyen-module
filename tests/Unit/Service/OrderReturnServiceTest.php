<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Codeception\PHPUnit\TestCase;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponsePaymentDetails;
use OxidSolutionCatalysts\Adyen\Service\OrderReturnService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;

class OrderReturnServiceTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderReturnService::isRedirectedFromAdyen
     * @dataProvider getDataProviderForIsRedirected
     */
    public function testIsRedirectedFromAdyen(
        bool $expectedResult,
        string $redirectResult,
        string $controller,
        string $function
    ) {
        $_GET['redirectResult'] = $redirectResult;
        $_GET['cl'] = $controller;
        $_GET['fnc'] = $function;

        $orderReturnService = $this->getServiceFromContainer(OrderReturnService::class);
        $actualResult = $orderReturnService->isRedirectedFromAdyen();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getDataProviderForIsRedirected()
    {
        return [
            [
                true,
                'redirectResult',
                'order',
                'return'
            ],
            [
                false,
                'redirectResult',
                'order',
                'notreturn'
            ],
            [
                false,
                'redirectResult',
                'notorder',
                'notreturn'
            ],
            [
                false,
                '',
                'notorder',
                'notreturn'
            ],
        ];
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderReturnService::getPaymentDetails
     */
    public function testGetPaymentDetails()
    {
        $expectedDetails = ['success' => true];
        $paymentDetailsMock = $this->createApiResponsePaymentDetailsMock(
            1,
            'redirectResult',
            $expectedDetails
        );

        $_GET['redirectResult'] = 'redirectResult';
        $orderReturnService = new OrderReturnService($paymentDetailsMock);
        $actualDetails = $orderReturnService->getPaymentDetails();

        $this->assertEquals($expectedDetails, $actualDetails);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OrderReturnService::isAuthorizedAmountSufficient
     * @dataProvider getDataProviderForAuthorizedAmount
     */
    public function testIsAuthorizedAmountSufficient(
        bool $expectedResult,
        array $paymentDetails,
        float $orderTotal
    ) {
        $orderReturnService = new OrderReturnService(
            $this->createApiResponsePaymentDetailsMock(0, '', [])
        );

        $this->assertSame(
            $expectedResult,
            $orderReturnService->isAuthorizedAmountSufficient($paymentDetails, $orderTotal)
        );
    }

    public function getDataProviderForAuthorizedAmount()
    {
        return [
            'authorized equals total' => [
                true,
                ['amount' => ['currency' => 'EUR', 'value' => 1000]],
                10.0,
            ],
            'authorized higher than total' => [
                true,
                ['amount' => ['currency' => 'EUR', 'value' => 2000]],
                10.0,
            ],
            'authorized lower than total (basket grew in parallel tab)' => [
                false,
                ['amount' => ['currency' => 'EUR', 'value' => 1000]],
                20.0,
            ],
            'no amount reported -> cannot compare, do not block' => [
                true,
                ['resultCode' => 'Authorised'],
                10.0,
            ],
            'non-positive order total -> do not block' => [
                true,
                ['amount' => ['currency' => 'EUR', 'value' => 1000]],
                0.0,
            ],
        ];
    }

    private function createApiResponsePaymentDetailsMock(
        int $getPaymentsDetailsInvokeAmount,
        $redirectResult,
        $paymentDetails
    ): AdyenAPIResponsePaymentDetails {
        $mock = $this->getMockBuilder(AdyenAPIResponsePaymentDetails::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPaymentDetails'])
            ->getMock();
        $mock->expects($this->exactly($getPaymentsDetailsInvokeAmount))
            ->method('getPaymentDetails')
            ->with(['details' => ['redirectResult' => $redirectResult]])
            ->willReturn($paymentDetails);

        return $mock;
    }
}
