<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Traits;

use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle;
use PHPUnit\Framework\TestCase;

class ParentMethodStubableTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Traits\ParentMethodStubableTrait::parentCall
     */
    public function test()
    {
        $sSearchArtNum = 'sSearchArtNum';

        $_GET['sSearchArtNum'] = $sSearchArtNum;

        $orderArticle = new OrderArticle();
        $actual = $orderArticle->parentCall('getSearchProductArtNr');

        $this->assertEquals($sSearchArtNum, $actual);
    }
}
