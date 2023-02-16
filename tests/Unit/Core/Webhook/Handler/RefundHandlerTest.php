<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core\Webhook\Handler;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\RefundHandler;

class RefundHandlerTest extends UnitTestCase
{
    use HandlerTestMockFactoryTrait;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\RefundHandler::additionalUpdates
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\RefundHandler::getAdyenAction
     * @covers \OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\RefundHandler::getAdyenStatus
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

        $orderMock = $this->createOrderMock(
            $orderId,
            1,
            'OK'
        );

        $eventMock = $this->createEventMock(
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference
        );

        /** @var RefundHandler $handlerMock */
        $handlerMock = $this->createHandlerMock(
            RefundHandler::class,
            $orderMock,
            $shopId,
            $orderId,
            $amountValue,
            $amountCurrency,
            $eventDate,
            $pspReference,
            $parentPspReference,
            Module::ADYEN_STATUS_REFUNDED,
            Module::ADYEN_ACTION_REFUND
        );
        $handlerMock->setData($eventMock);
        $handlerMock->updateStatus($eventMock);
    }
}
