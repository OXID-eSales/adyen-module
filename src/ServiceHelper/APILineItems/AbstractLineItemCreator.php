<?php

namespace OxidSolutionCatalysts\Adyen\ServiceHelper\APILineItems;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\EshopCommunity\Application\Model\Article;

abstract class AbstractLineItemCreator
{
    abstract public function createLineItem(?Article $article, BasketItem $basketItem): array;

    /**
     * https://docs.adyen.com/development-resources/currency-codes
     */
    protected function getPriceInMinorUnits(Article $article): string
    {
        return '' . $article->getPrice()->getBruttoPrice() * 100;
    }

    /**
     * https://docs.adyen.com/development-resources/currency-codes
     */
    protected function getVatInMinorUnits(Article $article): string
    {
        return '' . $article->getPrice()->getVat() * 100;
    }
}
