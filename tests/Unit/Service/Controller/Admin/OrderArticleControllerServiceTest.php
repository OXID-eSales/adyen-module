<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service\Controller\Admin;

use OxidSolutionCatalysts\Adyen\Service\Controller\Admin\OrderArticleControllerService;
use OxidSolutionCatalysts\Adyen\Model\Order;
use PHPUnit\Framework\TestCase;

class OrderArticleControllerServiceTest extends TestCase
{
    /**
     * @covers       \OxidSolutionCatalysts\Adyen\Service\Controller\Admin\OrderArticleControllerService::refundOrderIfNeeded
     * @dataProvider getTestData
     */
    public function testCancelPayment(
        bool $orderLoaded,
        float $amount,
        bool $isAdyenOrder,
        bool $isRefundPossible
    ) {
        $orderMock = $this->createOrderMock($orderLoaded, $amount, $isAdyenOrder, $isRefundPossible);
        $orderArticleControllerService = new OrderArticleControllerService();
        $orderArticleControllerService->refundOrderIfNeeded($orderLoaded, $amount, $orderMock);
    }

    public function getTestData(): array
    {
        return [
            [
                true,
                12.23,
                true,
                true,
            ],
            [
                false,
                12.23,
                true,
                true,
            ],
            [
                true,
                0,
                true,
                true,
            ],
            [
                true,
                12.23,
                false,
                true,
            ],
            [
                true,
                12.23,
                true,
                false,
            ],
        ];
    }

    private function createOrderMock(
        bool $orderLoaded,
        float $amount,
        bool $isAdyenOrder,
        bool $isRefundPossible
    ): Order {
        $mock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isAdyenOrder', 'isAdyenRefundPossible', 'refundAdyenOrder'])
            ->getMock();

        $mock->expects($this->exactly($orderLoaded && $amount > 0 ? 1 : 0))
            ->method('isAdyenOrder')
            ->willReturn($isAdyenOrder);

        $mock->expects($this->exactly($orderLoaded && $amount > 0 && $isAdyenOrder ? 1 : 0))
            ->method('isAdyenRefundPossible')
            ->willReturn($isRefundPossible);

        $mock->expects($this->exactly($orderLoaded && $amount > 0 && $isAdyenOrder && $isRefundPossible ? 1 : 0))
            ->method('refundAdyenOrder')
            ->with($amount);

        return $mock;
    }
}
