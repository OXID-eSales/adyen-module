<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;

class AdyenHistoryListTest extends UnitTestCase
{
    protected const TEST_ORDER_ID = '_testorder';

    protected function setUp(): void
    {
        parent::setUp();
        foreach ($this->providerTestHistoryData() as $dataSet) {
            [$historyId, $historyData] = $dataSet;
            $adyenHistory = oxNew(AdyenHistory::class);
            $adyenHistory->setId($historyId);
            $adyenHistory->assign($historyData);
            $adyenHistory->save();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestHistoryData() as $dataSet) {
            [$historyId,] = $dataSet;
            $adyenHistory = oxNew(AdyenHistory::class);
            $adyenHistory->load($historyId);
            $adyenHistory->delete();
        }
    }

    public function testGetAdyenHistoryListGetter(): void
    {
        $historyList = oxNew(AdyenHistoryList::class);
        $historyList->getAdyenHistoryList(self::TEST_ORDER_ID);
        $this->assertSame(2, $historyList->count());
        $testData = $this->providerTestHistoryData();
        $count = 0;
        foreach ($historyList->getArray() as $historyKey => $historyItem) {
            $this->assertInstanceOf(AdyenHistory::class, $historyItem);
            $this->assertSame((string)$testData[$count][0], (string)$historyKey);
            $this->assertSame(
                (string)$testData[$count][1][Module::ADYEN_HISTORY_TABLE . '__pspreference'],
                (string)$historyItem->{Module::ADYEN_HISTORY_TABLE . '__pspreference'}->value
            );
            $count++;
        }

        $orderId = $historyList->getOxidOrderIdByPSPReference("1");
        $this->assertEquals(self::TEST_ORDER_ID, $orderId);
        $orderId = $historyList->getOxidOrderIdByPSPReference("101");
        $this->assertNull($orderId);
    }

    public function providerTestHistoryData(): array
    {
        return [
            [
                '123',
                [
                    Module::ADYEN_HISTORY_TABLE . '__oxorderid' => self::TEST_ORDER_ID,
                    Module::ADYEN_HISTORY_TABLE . '__pspreference' => 1,
                    Module::ADYEN_HISTORY_TABLE . '__parentpspreference' => 1,
                ]
            ],
            [
                '456',
                [
                    Module::ADYEN_HISTORY_TABLE . '__oxorderid' => self::TEST_ORDER_ID,
                    Module::ADYEN_HISTORY_TABLE . '__pspreference' => 2,
                    Module::ADYEN_HISTORY_TABLE . '__parentpspreference' => 1,
                ]
            ]
        ];
    }
}
