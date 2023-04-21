<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CancelRefundHandler;

class CancelRefundHandlerTest extends UnitTestCase
{
    use HandlerTestMockFactoryTrait;

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

        /** @var CancelRefundHandler $handlerMock */
        $handlerMock = $this->createHandlerMock(
            CancelRefundHandler::class,
            $orderMock,
            $shopId,
            $orderId,
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference,
            Module::ADYEN_STATUS_CANCELLED,
            Module::ADYEN_ACTION_CANCEL
        );

        $handlerMock->setData($eventMock);
        $handlerMock->updateStatus($eventMock);
    }
}
