<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPITransactionInfoService;
use OxidSolutionCatalysts\Adyen\Service\CountryRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AdyenAPITransactionInfoServiceTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPITransactionInfoService::getTransactionJson
     */
    public function testGetTransactionJsonSuccess()
    {
        $logger = $this->createLoggerMock(0);
        $session = $this->createSessionMock(
            12.34,
            'EUR'
        );
        $countryRepo = $this->createCountryRepoMock('DE');
        $adyenAPITransactionInfoService = new AdyenAPITransactionInfoService(
            $session,
            $countryRepo,
            $logger
        );

        $this->assertSame(
            '{"currencyCode":"EUR","countryCode":"DE","totalPriceStatus":"FINAL","totalPrice":12.34}',
            $adyenAPITransactionInfoService->getTransactionJson()
        );
    }
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPITransactionInfoService::getTransactionJson
     */
    public function testGetTransactionJson()
    {
        $logger = $this->createLoggerMock(1);
        $session = $this->createSessionMock(
            12.34,
            "\xB1\x31" // malformed country code causes json encode error
        );
        $countryRepo = $this->createCountryRepoMock('DE');
        $adyenAPITransactionInfoService = new AdyenAPITransactionInfoService(
            $session,
            $countryRepo,
            $logger
        );

        $this->assertSame(
            '',
            $adyenAPITransactionInfoService->getTransactionJson()
        );
    }

    private function createLoggerMock(
        int $errorInvokeAmount
    ): LoggerInterface {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->exactly($errorInvokeAmount))
            ->method('error');

        return $loggerMock;
    }
    private function createSessionMock(
        float $grossTotalPrice,
        string $currencyIso
    ): Session {
        $priceMock = $this->createMock(Price::class);
        $priceMock->expects($this->once())
            ->method('getBruttoPrice')
            ->willReturn($grossTotalPrice);

        $currency = new \stdClass();
        $currency->name = $currencyIso;

        $basketMock = $this->createMock(Basket::class);
        $basketMock->expects($this->once())
            ->method('getPrice')
            ->willReturn($priceMock);
        $basketMock->expects($this->once())
            ->method('getBasketCurrency')
            ->willReturn($currency);

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->once())
            ->method('getBasket')
            ->willReturn($basketMock);

        return $sessionMock;
    }

    private function createCountryRepoMock(string $countryIso): CountryRepository
    {
        $countryRepoMock = $this->createMock(CountryRepository::class);
        $countryRepoMock->expects($this->once())
            ->method('getCountryIso')
            ->willReturn($countryIso);

        return $countryRepoMock;
    }
}
