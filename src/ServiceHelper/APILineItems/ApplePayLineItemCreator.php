<?php

namespace OxidEsales\EshopCommunity\modules\osc\adyen\src\ServiceHelper\APILineItems;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\EshopCommunity\Application\Model\Article;

class ApplePayLineItemCreator extends AbstractLineItemCreator
{
    public function createLineItem(?Article $article, BasketItem $basketItem): array
    {
        $lineItem = [
            'label' => $basketItem->getTitle(),
            'type' => 'final',
        ];

        if ($article) {
            $lineItem = array_merge(
                $lineItem,
                [
                    'amount' => $article->getPrice($basketItem->getAmount())->getBruttoPrice(),
                ]
            );
        }

        return $lineItem;
    }
}
