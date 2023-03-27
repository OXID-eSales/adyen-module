<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Session;
use Exception;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Wrapper\LoggerWrapper;
use PHPUnit\Framework\TestCase;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService;
use OxidSolutionCatalysts\Adyen\Core\Module;

class AdyenAPILineItemsServiceTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getLineItems
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getArticle
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getApplePayLineItem
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getPriceInMinorUnits
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getVatInMinorUnits
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::__construct
     * @dataProvider getAppleTestData
     */
    public function testGetLineItemsApple(array $expectedLineItems, $basketItems)
    {
        $exception = new Exception();
        $throwsException = false;
        $logger = $this->createLoggerMock($throwsException, $basketItems, $exception);
        $lineItemsService = new AdyenAPILineItemsService(
            $this->createSessionMock($basketItems, $throwsException, $exception),
            $logger
        );

        $actualLineItems = $lineItemsService->getLineItems(Module::PAYMENT_APPLE_PAY_ID);

        $this->assertEquals($expectedLineItems, $actualLineItems);
    }
    /**
     * @covers       \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getLineItems
     * @covers       \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getArticle
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getGooglePayLineItem
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getPriceInMinorUnits
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getVatInMinorUnits
     * @dataProvider getGoogleTestData
     */
    public function testGetLineItemsGoogle(array $expectedLineItems, $basketItems)
    {

        $exception = new Exception();
        $throwsException = false;
        $logger = $this->createLoggerMock($throwsException, $basketItems, $exception);

        $lineItemsService = new AdyenAPILineItemsService(
            $this->createSessionMock($basketItems, $throwsException, $exception),
            $logger
        );

        $actualLineItems = $lineItemsService->getLineItems(Module::PAYMENT_GOOGLE_PAY_ID);

        $this->assertEquals($expectedLineItems, $actualLineItems);
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getLineItems
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getArticle
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getGooglePayLineItem
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getPriceInMinorUnits
     * @covers \OxidSolutionCatalysts\Adyen\Service\AdyenAPILineItemsService::getVatInMinorUnits
     * @dataProvider getExceptionTestData
     */
    public function testThrowsException(array $expectedLineItems, $basketItems)
    {
        $exception = new Exception();
        $throwsException = true;
        $logger = $this->createLoggerMock($throwsException, $basketItems, $exception);

        $lineItemsService = new AdyenAPILineItemsService(
            $this->createSessionMock($basketItems, true, $exception),
            $logger
        );

        $actualLineItems = $lineItemsService->getLineItems(Module::PAYMENT_GOOGLE_PAY_ID);

        $this->assertEquals($expectedLineItems, $actualLineItems);
    }

    public function getAppleTestData(): array
    {
        return [
            [
                [
                    [
                        'label' => 'Article 1',
                        'type' => 'final',
                        'amount' => 20.00
                    ],
                    [
                        'label' => 'Article 2',
                        'type' => 'final',
                        'amount' => 30.0
                    ],
                ],
                [
                    [
                        'articleId' => '123',
                        'title' => 'Article 1',
                        'amount' => '2',
                        'bruttoPrice' => 20.00,
                        'vatPrice' => 1.9,
                    ],
                    [
                        'articleId' => '456',
                        'title' => 'Article 2',
                        'amount' => '1',
                        'bruttoPrice' => 30.00,
                        'vatPrice' => 1.9,
                    ],
                ],
            ]
        ];
    }

    public function getGoogleTestData(): array
    {
        return [
            [
                [
                    [
                        'quantity' => 2,
                        'description' => 'Article 1',
                        'amountIncludingTax' => '2000',
                        'taxPercentage' => '190',
                        'id' => '123',
                    ],
                    [
                        'quantity' => 1,
                        'description' => 'Article 2',
                        'amountIncludingTax' => '3000',
                        'taxPercentage' => '190',
                        'id' => '456',
                    ],
                ],
                [
                    [
                        'articleId' => '123',
                        'title' => 'Article 1',
                        'amount' => '2',
                        'bruttoPrice' => 20.00,
                        'vatPrice' => 1.9,
                    ],
                    [
                        'articleId' => '456',
                        'title' => 'Article 2',
                        'amount' => '1',
                        'bruttoPrice' => 30.00,
                        'vatPrice' => 1.9,
                    ],
                ],
            ]
        ];
    }

    public function getExceptionTestData(): array
    {
        return [
            [
                [
                    [
                        'quantity' => 2,
                        'description' => 'Article 1',
                    ],
                    [
                        'quantity' => 1,
                        'description' => 'Article 2',
                    ],
                ],
                [
                    [
                        'articleId' => '123',
                        'title' => 'Article 1',
                        'amount' => '2',
                        'bruttoPrice' => 20.00,
                        'vatPrice' => 1.9,
                    ],
                    [
                        'articleId' => '456',
                        'title' => 'Article 2',
                        'amount' => '1',
                        'bruttoPrice' => 30.00,
                        'vatPrice' => 1.9,
                    ],
                ],
            ]
        ];
    }

    private function createLoggerMock(bool $throwsException, array $basketItems, Exception $exception): LoggerWrapper
    {
        $logger = $this->getMockBuilder(LoggerWrapper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['error'])
            ->getMock();
        $logger->expects($this->exactly($throwsException ? count($basketItems) : 0))
            ->method('error')
            ->with(
                AdyenAPILineItemsService::class
                . ' could not get article for basket item ',
                ['exception' => $exception]
            );

        return $logger;
    }

    private function createSessionMock(
        array $testData,
        bool $throwsException,
        Exception $exception
    ): Session {
        $basketMock = $this->createMock(Basket::class);
        $basketMock->expects($this->once())
            ->method('getContents')
            ->willReturn($this->createBasketItemsFromTestData($testData, $throwsException, $exception));

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->once())
            ->method('getBasket')
            ->willReturn($basketMock);

        return $sessionMock;
    }

    private function createBasketItemsFromTestData(
        array $testData,
        bool $throwsException,
        Exception $exception
    ): array {
        $basketItems = [];

        foreach ($testData as $testDatum) {
            $basketItems[] = $this->createBasketItem(
                $testDatum['articleId'],
                $testDatum['title'],
                $testDatum['amount'],
                $testDatum['bruttoPrice'],
                $testDatum['vatPrice'],
                $throwsException,
                $exception
            );
        }

        return $basketItems;
    }

    private function createBasketItem(
        string $articleId,
        string $title,
        int $amount,
        float $bruttoPrice,
        float $vatPrice,
        bool $throwsException,
        Exception $exception
    ): BasketItem {
        $priceMock = $this->getMockBuilder(Price::class)
            ->onlyMethods(['getBruttoPrice', 'getVat'])
            ->getMock();
        $priceMock->expects($this->any())
            ->method('getBruttoPrice')
            ->willReturn($bruttoPrice);
        $priceMock->expects($this->any())
            ->method('getVat')
            ->willReturn($vatPrice);

        $articleMock = $this->getMockBuilder(Article::class)
            ->onlyMethods(['getId', 'getPrice'])
            ->getMock();
        $articleMock->expects($this->any())
            ->method('getId')
            ->willReturn($articleId);
        $articleMock->expects($this->any())
            ->method('getPrice')
            ->willReturn($priceMock);

        $basketItemMock = $this->getMockBuilder(BasketItem::class)
            ->onlyMethods(['getAmount', 'getTitle', 'getArticle'])
            ->getMock();
        $basketItemMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($title);
        $basketItemMock->expects($this->any())
            ->method('getAmount')
            ->willReturn($amount);
        $invocationMocker = $basketItemMock->expects($this->any())
            ->method('getArticle');
        if ($throwsException) {
            $invocationMocker->willThrowException($exception);
        } else {
            $invocationMocker->willReturn($articleMock);
        }

        return $basketItemMock;
    }
}
