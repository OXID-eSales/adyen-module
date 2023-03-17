<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\tests\Unit\Service;

use Codeception\PHPUnit\TestCase;
use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\OxNewService;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class OxNewServiceTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\OxNewService::oxNew
     */
    public function testOxNew()
    {
        $oxNewService = $this->getServiceFromContainer(OxNewService::class);
        $order = $oxNewService->oxNew(Order::class);

        $this->assertInstanceOf(Order::class, $order);

        $itemType = 'oxobject2category';
        $relations = $oxNewService->oxNew(Element2ShopRelations::class, [$itemType]);

        $this->assertInstanceOf(Element2ShopRelations::class, $relations);
        // prove constructor args correctly passed
        $this->assertEquals($itemType, $relations->getItemType());
    }
}
