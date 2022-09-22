<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Controller\Admin;

use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderList;
use OxidEsales\TestingLibrary\UnitTestCase;

final class OrderListTest extends UnitTestCase
{
    public function setup(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testRender(): void
    {
        $controller = $this->createPartialMock(OrderList::class, []);
        $this->assertSame('order_list.tpl', $controller->render());
        // call render to fill viewData
        $controller->render();
        $viewData = $controller->getViewData();
        $this->assertSame('PSPREFERENCE', $viewData['asearch']['oscadyenhistory']);
    }

    public function testBuildSelectString(): void
    {
        $requestStub = $this->createPartialMock(Request::class, ['getRequestEscapedParameter']);
        $requestStub->method('getRequestEscapedParameter')
            ->withConsecutive(['addsearchfld'], ['addsearch'])
            ->willReturnOnConsecutiveCalls('oscadyenhistory', 'test');
        Registry::set(Request::class, $requestStub);

        $controller = $this->createPartialMock(OrderList::class, []);

        $listModel = oxNew(ListModel::class);
        $listModel->init('oxorder');
        $listObject = $listModel->getBaseObject();

        $result = $controller->_buildSelectString($listObject);

        $this->assertStringContainsString('left join oscadyenhistory', $result);
        $this->assertStringContainsString("oscadyenhistory.pspreference like '%test%'", $result);
    }
}
