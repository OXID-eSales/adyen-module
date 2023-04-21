<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\ServiceHelper;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\modules\osc\adyen\src\ServiceHelper\APILineItems\GooglePayLineItemCreator;
use PHPUnit\Framework\TestCase;

class GooglePayLineItemCreatorTest extends TestCase
{
    /**
     * @covers \OxidEsales\EshopCommunity\modules\osc\adyen\src\ServiceHelper\APILineItems\GooglePayLineItemCreator::createLineItem
     * @covers \OxidEsales\EshopCommunity\modules\osc\adyen\src\ServiceHelper\APILineItems\AbstractLineItemCreator::getPriceInMinorUnits
     * @covers \OxidEsales\EshopCommunity\modules\osc\adyen\src\ServiceHelper\APILineItems\AbstractLineItemCreator::getVatInMinorUnits
     *
     * @dataProvider getTestData
     */
    public function testCreateLineItem(
        int $amount,
        bool $articleIsNull,
        string $title,
        float $grossPrice,
        float $vat,
        string $priceMinorUnits,
        string $tatMinorUnits,
        string $articleId,
        array $expectedArray
    ) {
        $article = $articleIsNull ? null : $this->createArticleMock($amount, $grossPrice, $vat, $articleId);
        $basketItem = $this->createBasketItemMock($amount, $title);

        $applePayLineItemCreator = new GooglePayLineItemCreator();
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
        $vat = 1.23;
        $title = 'new fancy dancy';
        $priceMinorUnits = '1223';
        $vatMinorUnits = '123';
        $articleId = 'SKU123';

        return [
            [
                $amount,
                false,
                $title,
                $grossPrice,
                $vat,
                $priceMinorUnits,
                $vatMinorUnits,
                $articleId,
                [
                    'quantity' => $amount,
                    'description' => $title,
                    'amountIncludingTax' => $priceMinorUnits,
                    'taxPercentage' => $vatMinorUnits,
                    'id' => $articleId,
                ]
            ],
            [
                $amount,
                true,
                $title,
                $grossPrice,
                $vat,
                $priceMinorUnits,
                $vatMinorUnits,
                $articleId,
                [
                    'quantity' => $amount,
                    'description' => $title,
                ]
            ],
        ];
    }

    private function createArticleMock(int $amount, float $grossPrice, float $vat, string $id): Article
    {
        $article = $this->getMockBuilder(Article::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPrice', 'getId'])
            ->getMock();

        $article->expects($this->exactly(2))
            ->method('getPrice')
            ->willReturn($this->createPriceMock($grossPrice, $vat));
        $article->expects($this->exactly(1))
            ->method('getId')
            ->willReturn($id);

        return $article;
    }

    private function createPriceMock(float $grossPrice, float $vat): Price
    {
        $price = $this->getMockBuilder(Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBruttoPrice', 'getVat'])
            ->getMock();

        $price->expects($this->once())
            ->method('getBruttoPrice')
            ->willReturn($grossPrice);

        $price->expects($this->once())
            ->method('getVat')
            ->willReturn($vat);

        return $price;
    }

    private function createBasketItemMock(int $amount, string $title): BasketItem
    {
        $basketItem = $this->getMockBuilder(BasketItem::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getAmount'])
            ->getMock();

        $basketItem->expects($this->once())
            ->method('getTitle')
            ->willReturn($title);
        $basketItem->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);

        return $basketItem;
    }
}
