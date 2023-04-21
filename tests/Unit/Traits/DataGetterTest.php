<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use PHPUnit\Framework\TestCase;

class DataGetterTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\DataGetter::getAdyenFloatData
     */
    public function testGetFloatRequestData()
    {
        $key = 'price';
        $valueInternal = '1.23';
        $valueExpected = 1.23;

        $adyenHistory = $this->createAdyenHistoryMock($key, $valueInternal);
        $this->assertEquals(
            $valueExpected,
            $adyenHistory->getAdyenFloatData($key)
        );
    }

    private function createAdyenHistoryMock(string $key, string $value): AdyenHistory
    {
        $adyenHistoryMock = $this->getMockBuilder(AdyenHistory::class)
            ->onlyMethods(['getAdyenStringData'])
            ->getMock();
        $adyenHistoryMock->expects($this->once())
            ->method('getAdyenStringData')
            ->with($key)
            ->willReturn($value);

        return $adyenHistoryMock;
    }
}
