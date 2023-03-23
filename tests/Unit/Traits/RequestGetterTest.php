<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidEsales\EshopCommunity\Core\Request;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use PHPUnit\Framework\TestCase;

class RequestGetterTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\RequestGetter::getStringRequestData
     */
    public function testGetStringRequestData()
    {
        $key = 'name';
        $value = 'nina';

        $requestGetter = $this->createRequestGetterMock($key, $value);
        $this->assertEquals(
            $value,
            $requestGetter->getStringRequestData($key)
        );
    }
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\RequestGetter::getFloatRequestData
     */
    public function testGetFloatRequestData()
    {
        $key = 'price';
        $valueInternal = '1.23';
        $valueExpected = 1.23;

        $requestGetter = $this->createRequestGetterMock($key, $valueInternal);
        $this->assertEquals(
            $valueExpected,
            $requestGetter->getFloatRequestData($key)
        );
    }

    private function createRequestGetterMock(string $key, string $value): PaymentController
    {
        $oxNewServiceMock = $this->getMockBuilder(PaymentController::class)
            ->onlyMethods(['getServiceFromContainer'])
            ->getMock();
        $oxNewServiceMock->expects($this->once())
            ->method('getServiceFromContainer')
            ->with(OxNewService::class)
            ->willReturn($this->createOxNewServiceMock($key, $value));

        return $oxNewServiceMock;
    }

    private function createOxNewServiceMock(string $key, string $value): OxNewService
    {
        $oxNewServiceMock = $this->getMockBuilder(OxNewService::class)
            ->onlyMethods(['oxNew'])
            ->getMock();
        $oxNewServiceMock->expects($this->once())
            ->method('oxNew')
            ->with(Request::class)
            ->willReturn($this->createRequestMock($key, $value));

        return $oxNewServiceMock;
    }

    protected function createRequestMock(string $key, string $value): Request
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getRequestParameter'])
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getRequestParameter')
            ->with($key, '')
            ->willReturn($value);

        return $requestMock;
    }
}
