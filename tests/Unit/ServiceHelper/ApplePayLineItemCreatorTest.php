<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\ServiceHelper;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidSolutionCatalysts\Adyen\ServiceHelper\APILineItems\ApplePayLineItemCreator;
use PHPUnit\Framework\TestCase;

class ApplePayLineItemCreatorTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\ServiceHelper\APILineItems\ApplePayLineItemCreator::createLineItem
     *
     * @dataProvider getTestData
     */
    public function testCreateLineItem(
        int $amount,
        bool $articleIsNull,
        string $title,
        float $grossPrice,
        array $expectedArray
    ) {
        $article = $articleIsNull ? null : $this->createArticleMock($amount, $grossPrice);
        $basketItem = $this->createBasketItemMock($amount, $title, $articleIsNull);

        $applePayLineItemCreator = new ApplePayLineItemCreator();
        $lineItems = $applePayLineItemCreator->createLineItem($article, $basketItem);

        $this->assertEquals(
            $expectedArray,
            $lineItems
        );
    }

    public function getTestData()
    {
        $amount = 2;
        $grossPrice = 12.23;
        $title = 'new fancy dancy';

        return [
            [
                $amount,
                false,
                $title,
                $grossPrice,
                [
                    'label' => $title,
                    'type' => 'final',
                    'amount' => $grossPrice,
                ]
            ],
            [
                $amount,
                true,
                $title,
                $grossPrice,
                [
                    'label' => $title,
                    'type' => 'final'
                ]
            ],
        ];
    }

    private function createArticleMock(int $amount, float $grossPrice): Article
    {
        $article = $this->getMockBuilder(Article::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPrice'])
            ->getMock();

        $article->expects($this->once())
            ->method('getPrice')
            ->with($amount)
            ->willReturn($this->createPriceMock($amount, $grossPrice));

        return $article;
    }

    private function createPriceMock(int $amount, float $grossPrice): Price
    {
        $price = $this->getMockBuilder(Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBruttoPrice'])
            ->getMock();

        $price->expects($this->once())
            ->method('getBruttoPrice')
            ->willReturn($grossPrice);

        return $price;
    }

    private function createBasketItemMock(int $amount, string $title, bool $articleIsNull): BasketItem
    {
        $basketItem = $this->getMockBuilder(BasketItem::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getAmount'])
            ->getMock();

        $basketItem->expects($this->once())
            ->method('getTitle')
            ->willReturn($title);
        $basketItem->expects($articleIsNull ? $this->never() : $this->once())
            ->method('getAmount')
            ->willReturn($amount);

        return $basketItem;
    }
}
