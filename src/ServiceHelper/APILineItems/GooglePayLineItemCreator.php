<?php

namespace OxidSolutionCatalysts\Adyen\ServiceHelper\APILineItems;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\EshopCommunity\Application\Model\Article;

class GooglePayLineItemCreator extends AbstractLineItemCreator
{
    public function createLineItem(?Article $article, BasketItem $basketItem): array
    {
        $lineItem = [
            'quantity' => $basketItem->getAmount(),
            'description' => $basketItem->getTitle(),
        ];

        if ($article) {
            $lineItem = array_merge(
                $lineItem,
                [
                    'amountIncludingTax' => $this->getPriceInMinorUnits($article),
                    'taxPercentage' => $this->getVatInMinorUnits($article),
                    'id' => $article->getId(),
                ]
            );
        }

        return $lineItem;
    }
}
