<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Core\Webhook\Handler;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\Context;
use PHPUnit\Framework\MockObject\MockObject;

class CancelRefundHandlerTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler::additionalUpdates
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler::getAdyenAction
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler::getAdyenStatus
     */
    public function testAdditionalUpdates()
    {
        $amountValue = 1.23;
        $amountCurrency = 'EUR';
        $eventDate = '20230216';
        $orderId = 'orderId';
        $shopId = 1;
        $pspReference = 'pspReference';
        $parentPspReference = 'parentPspReference';

        $orderMock = $this->createOrderMock($orderId);

        $eventMock = $this->createEventMock(
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference
        );

        $builder = $this->getMockBuilder(CancelRefundHandler::class);
        $builder->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    null,
                    $orderMock,
                    null,
                    $this->createContextMock($shopId)
                ]
            );
        $builder->onlyMethods(
            ['setHistoryEntry', 'getOrderByAdyenPSPReference']
        );
        $handlerMock = $builder->getMock();
        $handlerMock->expects($this->once())
            ->method('setHistoryEntry')
            ->with(
                $orderId,
                $shopId,
                $amountValue,
                $amountCurrency,
                $eventDate,
                $pspReference,
                $parentPspReference,
                Module::ADYEN_STATUS_CANCELLED,
                Module::ADYEN_ACTION_CANCEL
            );
        $handlerMock->expects($this->once())
            ->method('getOrderByAdyenPSPReference')
            ->willReturn($orderMock);

        $handlerMock->setData($eventMock);
        $handlerMock->updateStatus($eventMock);
    }

    private function createOrderMock(string $orderId): MockObject
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->once())
            ->method('getId')
            ->willReturn($orderId);

        return $orderMock;
    }

    private function createEventMock(
        float $amountValue,
        string $amountCurrency,
        string $eventDate,
        string $pspReference,
        string $parentPspReference
    ) {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getAmountValue')
            ->willReturn($amountValue);
        $eventMock->expects($this->once())
            ->method('getAmountCurrency')
            ->willReturn($amountCurrency);
        $eventMock->expects($this->once())
            ->method('getEventDate')
            ->willReturn($eventDate);
        $eventMock->expects($this->once())
            ->method('getPspReference')
            ->willReturn($pspReference);
        $eventMock->expects($this->exactly(2))
            ->method('getParentPspReference')
            ->willReturn($parentPspReference);

        return $eventMock;
    }

    private function createContextMock(int $shopId): MockObject
    {
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())
            ->method('getCurrentShopId')
            ->willReturn($shopId);

        return $contextMock;
    }
}
