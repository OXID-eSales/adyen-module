<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use Adyen\Service\Checkout;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPIResponse;
use OxidSolutionCatalysts\Adyen\Service\AdyenSDKLoader;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * this class is a parent class for all AdyenAPIResponse*Test and holds common methods used in this tests
 */
abstract class AbstractAdyenAPIResponseTest extends TestCase
{
    use ServiceContainer;

    protected function createAdyenAPIResponse(
        string $apiResponseFQCN,
        Checkout $checkoutService,
        int $errorInvokeAmount,
        int $paymentsExceptionInvokeAmount,
        string $exceptionMessage
    ): AdyenAPIResponse {
        $adyenSDKMock = $this->createMock(AdyenSDKLoader::class);
        $sessionSettingsMock = $this->createMock(SessionSettings::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $exception = new Exception($exceptionMessage);

        $loggerMock->expects($this->exactly($errorInvokeAmount))
            ->method('error')
            ->with($exceptionMessage, ['exception' => $exception]);


        $mock = $this->getMockBuilder($apiResponseFQCN)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$adyenSDKMock, $sessionSettingsMock, $loggerMock])
            ->onlyMethods(['createCheckout', 'getPaymentsNotFoundException'])
            ->getMock();
        $mock->expects($this->once())
            ->method('createCheckout')
            ->willReturn($checkoutService);
        $mock->expects($this->exactly($paymentsExceptionInvokeAmount))
            ->method('getPaymentsNotFoundException')
            ->willReturn($exception);

        /** @var AdyenAPIResponse $mock */
        return $mock;
    }

    protected function createCheckoutServiceMock(
        array $params,
        string $method,
        $result
    ): Checkout {
        $checkoutServiceMock = $this->createMock(Checkout::class);
        $checkoutServiceMock->expects($this->once())
            ->method($method)
            ->with($params)
            ->willReturn($result);

        return $checkoutServiceMock;
    }

    protected function createAdyenAPIMock(
        array $params,
        string $adyenAPIFQCN,
        string $method
    ) {
        $adyenAPIRefundsMock = $this->createMock($adyenAPIFQCN);
        $adyenAPIRefundsMock->expects($this->once())
            ->method($method)
            ->willReturn($params);

        return $adyenAPIRefundsMock;
    }
}
