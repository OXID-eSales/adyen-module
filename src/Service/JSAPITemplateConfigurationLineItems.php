<?php

namespace OxidSolutionCatalysts\Adyen\Service;


use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Application\Model\Article;
use Psr\Log\LoggerInterface;

class JSAPITemplateConfigurationLineItems
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

    public function getLineItems(
    ): array {
        $lineItems = [];
        $basketItems = $this->session->getBasket()->getContents();

        foreach ($basketItems as $basketItem) {
            $article = $this->getArticle($basketItem);

            $lineItems[] = [
                'quantity' => $basketItem->getAmount(),
                'description' => $basketItem->getTitle(),
                'amountIncludingTax' => $this->getPriceInMinorUnits($article),
                'taxPercentage' => $this->getVatInMinorUnits($article),
                'id' => $article->getId(),
            ];
        }

        return $lineItems;
    }

    private function getArticle(BasketItem $basketItem): Article
    {
        try {
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
        return (string) $article->getPrice()->getBruttoPrice() * 100;
    }

    /**
     * https://docs.adyen.com/development-resources/currency-codes
     */
    private function getVatInMinorUnits(Article $article): string
    {
        return (string) $article->getPrice()->getVat() * 100;
    }
}
