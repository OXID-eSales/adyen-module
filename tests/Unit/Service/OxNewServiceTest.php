<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use Codeception\PHPUnit\TestCase;
use OxidEsales\EshopCommunity\Core\Model\ListModel;
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

        $itemType = 'oxorder';
        $relations = $oxNewService->oxNew(ListModel::class, [$itemType]);

        $this->assertInstanceOf(ListModel::class, $relations);
        // prove constructor args correctly passed
        $this->assertInstanceOf(Order::class, $relations->getBaseObject());
    }
}
