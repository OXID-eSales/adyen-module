<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Session;
use Psr\Log\LoggerInterface;

class AdyenAPITransactionInfoService
{
    private Session $session;
    private LoggerInterface $logger;
    private CountryRepository $countryRepository;

    public function __construct(
        Session $session,
        CountryRepository $countryRepository,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->countryRepository = $countryRepository;
    }

    public function getTransactionJson(): string
    {
        $basket = $this->session->getBasket();
        $grossTotalPrice = $basket->getPrice()->getBruttoPrice();
        $currency = $basket->getBasketCurrency()->name;
        $userCountryIso = $this->countryRepository->getCountryIso();

        $transactionItem = [
            'currencyCode' => $currency,
            'countryCode' => $userCountryIso,
            'totalPriceStatus' => 'FINAL',
            'totalPrice' => $grossTotalPrice,
        ];

        $json = json_encode($transactionItem, true);

        if ($json === false) {
            $this->logger->error(
                sprintf(
                    self::class . '::getTransactionJson could not encode transaction item to json'
                )
            );

            $json = '';
        }

        return $json;
    }
}
