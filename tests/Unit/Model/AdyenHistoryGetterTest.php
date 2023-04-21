<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\MockObject\MockObject;

class AdyenHistoryGetterTest extends UnitTestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getOrderId
     */
    public function testGetOrderId()
    {
        $key = 'orderid';
        $returnValue = 'someId';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getOrderId()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getParentPSPReference
     */
    public function testGetParentPSPReference()
    {
        $key = 'parentpspreference';
        $returnValue = 'someValue';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getParentPSPReference()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getCurrency
     */
    public function testGetCurrency()
    {
        $key = 'currency';
        $returnValue = 'someValue';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getCurrency()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getAdyenStatus
     */
    public function testGetAdyenStatus()
    {
        $key = 'adyenstatus';
        $returnValue = 'someValue';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getAdyenStatus()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getAdyenAction
     */
    public function testGetAdyenAction()
    {
        $key = 'adyenaction';
        $returnValue = 'someValue';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getAdyenAction()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getTimeStamp
     */
    public function testGetTimeStamp()
    {
        $key = 'oxtimestamp';
        $returnValue = 'someValue';

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockStringGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getTimeStamp()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getFormatedPrice
     */
    public function testGetFormatedPrice()
    {
        $price = 1234.56;
        $priceFormatted = '1.234,56';

        /** @var AdyenHistory $adyenHistoryMock */
        $builder = $this->getMockBuilder(AdyenHistory::class)
            ->disableOriginalConstructor();
        $builder->onlyMethods(['getPrice']);
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('getPrice')
            ->with()
            ->willReturn($price);

        $this->assertEquals(
            $priceFormatted,
            $adyenHistoryMock->getFormatedPrice()
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::getPrice
     */
    public function testGetPrice()
    {
        $key = 'oxprice';
        $returnValue = 2.45;

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock = $this->createAdyenHistoryMockFloatGetter(
            $key,
            $returnValue
        );

        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->getPrice()
        );
    }

    private function createAdyenHistoryMockStringGetter(
        string $key,
        string $value
    ): MockObject {
        $builder = $this->getMockBuilder(AdyenHistory::class)
            ->disableOriginalConstructor();
        $builder->onlyMethods(['getAdyenStringData']);
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with($key)
            ->willReturn($value);

        return $adyenHistoryMock;
    }

    private function createAdyenHistoryMockFloatGetter(
        string $key,
        float $value
    ): MockObject {
        $builder = $this->getMockBuilder(AdyenHistory::class)
            ->disableOriginalConstructor();
        $builder->onlyMethods(
            [
                'getAdyenFloatData',
            ]
        );
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('getAdyenFloatData')
            ->with($key)
            ->willReturn($value);

        return $adyenHistoryMock;
    }
}
