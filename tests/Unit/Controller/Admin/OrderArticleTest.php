<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle;
use OxidSolutionCatalysts\Adyen\Service\Controller\Admin\OrderArticleControllerService;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use PHPUnit\Framework\TestCase;
use OxidSolutionCatalysts\Adyen\Model\Order as AdyenOrder;

class OrderArticleTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::storno
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::collectAmountForAdyenRefund
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::runAdyenRefund
     * @dataProvider getStornoTestData
     */
    public function testStorno($testData)
    {
        $orderArticleController = $this->createStornoOrDeleteControllerMock(
            $testData['amountBefore'],
            $testData['amountAfter'],
            $testData['orderLoaded'],
            $testData['orderId'],
            'storno'
        );
        $orderArticleController->storno();
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::deleteThisArticle
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::collectAmountForAdyenRefund
     * @covers \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle::runAdyenRefund
     * @dataProvider getStornoTestData
     */
    public function testDeleteThisArticle($testData)
    {
        $orderArticleController = $this->createStornoOrDeleteControllerMock(
            $testData['amountBefore'],
            $testData['amountAfter'],
            $testData['orderLoaded'],
            $testData['orderId'],
            'deleteThisArticle'
        );
        $orderArticleController->deleteThisArticle();
    }

    public function getStornoTestData(): array
    {
        return [
            [
                [
                    'amountBefore' => 12.23,
                    'amountAfter' => 12.23,
                    'orderLoaded' => true,
                    'orderId' => '1234',
                ],
            ],
            [
                [
                    'amountBefore' => 12.23,
                    'amountAfter' => 12.23,
                    'orderLoaded' => false,
                    'orderId' => '1234',
                ],
            ],
        ];
    }

    private function createStornoOrDeleteControllerMock(
        float $amountBefore,
        float $amountAfter,
        bool $orderLoaded,
        string $orderId,
        string $parentCallMethodName
    ): OrderArticle {
        $orderArticleController = $this->getMockBuilder(OrderArticle::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getServiceFromContainer',
                    'parentCall',
                    'getEditObjectId'
                ]
            )
            ->getMock();

        $orderMock = $this->createOrderMock($orderId, $orderLoaded);
        $oxNewServiceMock = $this->createOxNewServiceMock($orderMock);

        $getServiceReturnValueMap = [
            OxNewService::class => $oxNewServiceMock,
            OrderArticleControllerService::class => $this->createOrderArticleControllerServiceMock(
                $orderLoaded,
                $amountBefore - $amountAfter,
                $orderMock
            ),
        ];

        $orderArticleController->expects($this->exactly(4))
            ->method('getServiceFromContainer')
            ->willReturnCallback(fn($argument) => $getServiceReturnValueMap[$argument]);
        $orderArticleController->expects($this->once())
            ->method('parentCall')
            ->with($parentCallMethodName);
        $orderArticleController->expects($this->exactly(3))
            ->method('getEditObjectId')
            ->willReturn($orderId);

        return $orderArticleController;
    }

    private function createOxNewServiceMock(AdyenOrder $order): OxNewService
    {
        $mock = $this->getMockBuilder(OxNewService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['oxNew'])
            ->getMock();
        $mock->expects($this->exactly(3))
            ->method('oxNew')
            ->with(Order::class)
            ->willReturn($order);

        return $mock;
    }

    private function createOrderArticleControllerServiceMock(
        bool $orderLoaded,
        float $amount,
        AdyenOrder $order
    ): OrderArticleControllerService {
        $mock = $this->getMockBuilder(OrderArticleControllerService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['refundOrderIfNeeded'])
            ->getMock();
        $mock->expects($this->exactly(1))
            ->method('refundOrderIfNeeded')
            ->with($orderLoaded, $amount, $order);

        return $mock;
    }

    private function createOrderMock(
        string $orderId,
        bool $orderLoaded
    ): AdyenOrder {
        $mock = $this->getMockBuilder(AdyenOrder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'load',
                    'getTotalOrderSum'
                ]
            )
            ->getMock();
        $mock->expects($this->exactly(3))
            ->method('load')
            ->with($orderId)
            ->willReturn($orderLoaded);
        $mock->expects($orderLoaded ? $this->exactly(2) : $this->never())
            ->method('getTotalOrderSum')
            ->willReturn($orderId);

        return $mock;
    }
}
