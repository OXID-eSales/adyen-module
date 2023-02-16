<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use PHPUnit\Framework\MockObject\MockObject;

class AdyenHistoryLoadByIdentTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::loadByIdent
     */
    public function testLoadByIdentSuccess()
    {
        $var = 'pspreference';
        $returnValue = true;
        $shopId = 1;
        $adyenHistoryId = 'adyenHistoryId';
        $blMallUsersConfigValue = false;

        $adyenHistoryMock = $this->createAdyenHistoryMock(
            $var,
            $shopId,
            $adyenHistoryId,
            $blMallUsersConfigValue,
            $returnValue,
            1,
            1
        );

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock->init(Module::ADYEN_HISTORY_TABLE, false);
        // we cant call loadByIdent directly
        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->loadByPSPReference($var)
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\AdyenHistory::loadByIdent
     */
    public function testLoadByIdentBlMallUsersTrue()
    {
        $var = 'pspreference';
        $returnValue = true;
        $shopId = 1;
        $adyenHistoryId = 'adyenHistoryId';
        $blMallUsersConfigValue = true;

        $adyenHistoryMock = $this->createAdyenHistoryMock(
            $var,
            $shopId,
            $adyenHistoryId,
            $blMallUsersConfigValue,
            $returnValue,
            0,
            0
        );

        /** @var AdyenHistory $adyenHistoryMock */
        $adyenHistoryMock->init(Module::ADYEN_HISTORY_TABLE, false);
        // we cant call loadByIdent directly
        $this->assertEquals(
            $returnValue,
            $adyenHistoryMock->loadByPSPReference($var)
        );
    }

    private function createQueryBuilderFactoryMock(
        string $var,
        int $shopId,
        string $adyenHistoryId,
        int $andWhereInvokeAmount,
        bool $blMallUsersConfigValue
    ): MockObject {
        $factoryMock = $this->createMock(QueryBuilderFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->createQueryBuilderMock(
                    $var,
                    $shopId,
                    $adyenHistoryId,
                    $andWhereInvokeAmount,
                    $blMallUsersConfigValue
                )
            );

        return $factoryMock;
    }

    private function createQueryBuilderMock(
        string $var,
        int $shopId,
        string $adyenHistoryId,
        int $andWhereInvokeAmount,
        bool $blMallUsersConfigValue
    ): MockObject {
        $parameters = $blMallUsersConfigValue ?
            ['var' => $var]
        :
            ['var' => $var, 'oxshopid' => $shopId];

        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->expects($this->once())
            ->method('select')
            ->with('oxid')
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->once())
            ->method('from')
            ->with(Module::ADYEN_HISTORY_TABLE)
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->once())
            ->method('setMaxResults')
            ->with(1)
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->once())
            ->method('where')
            ->with("{$var} = :var")
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->exactly($andWhereInvokeAmount))
            ->method('andWhere')
            ->with('oxshopid = :oxshopid')
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->once())
            ->method('setParameters')
            ->with($parameters)
            ->willReturn($this->returnSelf());
        $queryBuilderMock->expects($this->once())
            ->method('execute')
            ->willReturn($this->createDBResultMock($adyenHistoryId));

        return $queryBuilderMock;
    }

    private function createContextMock(
        int $shopId,
        int $getCurrentShopIdInvokeAmount
    ): MockObject {
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock->expects($this->exactly($getCurrentShopIdInvokeAmount))
            ->method('getCurrentShopId')
            ->willReturn($shopId);

        return $contextMock;
    }

    private function createDBResultMock(
        string $adyenHistoryId
    ): MockObject {
        $resultMock = $this->createMock(Result::class);
        $resultMock->expects($this->once())
            ->method('fetchOne')
            ->willReturn($adyenHistoryId);

        return $resultMock;
    }

    private function createConfigMock(
        int $getConfigParamInvokeAmount,
        string $configKey,
        $configValue
    ): MockObject {
        $resultMock = $this->createMock(Config::class);
        $resultMock->expects($this->exactly($getConfigParamInvokeAmount))
            ->method('getConfigParam')
            ->with($configKey)
            ->willReturn($configValue);

        return $resultMock;
    }

    private function createAdyenHistoryMock(
        string $var,
        int $shopId,
        string $adyenHistoryId,
        bool $blMallUsersConfigValue,
        bool $returnValue,
        int $andWhereInvokeAmount,
        int $getCurrentShopIdInvokeAmount
    ): MockObject {
        $builder = $this->getMockBuilder(AdyenHistory::class)
            ->disableOriginalConstructor();
        $builder->onlyMethods(
            [
                'getServiceFromContainer',
                'load',
                'getConfigFromRegistry'
            ]
        );
        $adyenHistoryMock = $builder->getMock();
        $adyenHistoryMock->expects($this->exactly(2))
            ->method('getServiceFromContainer')
            ->willReturnOnConsecutiveCalls(
                $this->createQueryBuilderFactoryMock(
                    $var,
                    $shopId,
                    $adyenHistoryId,
                    $andWhereInvokeAmount,
                    $blMallUsersConfigValue
                ),
                $this->createContextMock($shopId, $getCurrentShopIdInvokeAmount)
            );
        $adyenHistoryMock->expects($this->once())
            ->method('load')
            ->with($adyenHistoryId)
            ->willReturn($returnValue);
        $adyenHistoryMock->expects($this->once())
            ->method('getConfigFromRegistry')
            ->willReturn(
                $this->createConfigMock(
                    1,
                    'blMallUsers',
                    $blMallUsersConfigValue
                )
            );

        return $adyenHistoryMock;
    }
}
