<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Integration\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;

class AdyenHistoryListTest extends UnitTestCase
{
    private const TEST_ORDER_ID = '_testorder';

    public function setup(): void
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
        $this->cleanUpTable(Module::ADYEN_HISTORY_TABLE);

        parent::tearDown();
    }

    public function testGetAdyenHistoryList(): void
    {
        $historyList = $this->createPartialMock(AdyenHistoryList::class, []);
        $historyList->init(AdyenHistory::class);
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
