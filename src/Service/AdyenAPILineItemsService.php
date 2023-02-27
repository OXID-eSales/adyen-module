<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Application\Model\Article;
use Psr\Log\LoggerInterface;

class AdyenAPILineItemsService
{
    private Session $session;
    private LoggerInterface $logger;

    public function __construct(
        Session $session,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->logger = $logger;
    }

    public function getLineItems(): array
    {
        $lineItems = [];
        $basketItems = $this->session->getBasket()->getContents();

        foreach ($basketItems as $basketItem) {
            $article = $this->getArticle($basketItem);

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

            $lineItems[] = $lineItem;
        }

        return $lineItems;
    }

    private function getArticle(BasketItem $basketItem): ?Article
    {
        $article = null;

        try {
            /** @var Article $article */
            $article = $basketItem->getArticle();
        } catch (\Exception $exception) {
            $this->logger->error(
                self::class . ' could not get article for basket item ' . $basketItem->getProductId(),
                ['exception' => $exception]
            );
        }

        return $article;
    }

    /**
     * https://docs.adyen.com/development-resources/currency-codes
     */
    private function getPriceInMinorUnits(Article $article): string
    {
        return '' . $article->getPrice()->getBruttoPrice() * 100;
    }

    /**
     * https://docs.adyen.com/development-resources/currency-codes
     */
    private function getVatInMinorUnits(Article $article): string
    {
        return '' . $article->getPrice()->getVat() * 100;
    }
}
