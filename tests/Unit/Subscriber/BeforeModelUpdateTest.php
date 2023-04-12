<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Subscriber;

use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use OxidSolutionCatalysts\Adyen\Subscriber\BeforeModelUpdate;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

class BeforeModelUpdateTest extends TestCase
{
    use ServiceContainer;

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Subscriber\BeforeModelUpdate::getSubscribedEvents
     */
    public function test()
    {
        $this->assertEquals(
            [
                BeforeModelUpdateEvent::class => 'handle',
            ],
            BeforeModelUpdate::getSubscribedEvents()
        );
    }
}
