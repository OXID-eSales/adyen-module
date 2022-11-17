<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Core\Webhook\Handler;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\CaptureHandler;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CaptureHandlerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $order = oxNew(Order::class);
        $order->setAdyenPSPReference("YOUR_PSP_REFERENCE");
        $order->save();

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId(Registry::getConfig()->getShopId());
        $adyenHistory->setPrice(1000);
        $adyenHistory->setTimeStamp("2021-01-01T01:00:00+01:00");
        $adyenHistory->setPSPReference("YOUR_PSP_REFERENCE");
        $adyenHistory->setAdyenStatus("AUTHORISATION");
        $adyenHistory->save();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpTable(Module::ADYEN_HISTORY_TABLE);
    }

    public function testUpdateStatus()
    {
        $captureHandler = oxNew(CaptureHandler::class);
        $captureHandler->updateStatus($this->proceedNotificationRequestsItem());

        $historyList = oxNew(AdyenHistoryList::class);
        $historyList->init(AdyenHistory::class);
        $orderId = $historyList->getOxidOrderIdByPSPReference("YOUR_PSP_REFERENCE");

        $this->assertNotNull($orderId);
    }

    public function testHandle()
    {
        $data = [
            "live" => "false",
            "notificationItems" => [
                $this->proceedNotificationRequestsItem()
            ]
        ];
        $event = oxNew(Event::class, $data);

        $authorisationHandler = oxNew(CaptureHandler::class);
        $authorisationHandler->handle($event);

        $historyList = oxNew(AdyenHistoryList::class);
        $historyList->init(AdyenHistory::class);
        $orderId = $historyList->getOxidOrderIdByPSPReference("YOUR_PSP_REFERENCE");

        $this->assertNotNull($orderId);
    }

    private function proceedNotificationRequestsItem()
    {
        /** @var ModuleSettings $moduleSettings */
        try {
            $moduleSettings = ContainerFactory::getInstance()
                ->getContainer()
                ->get(ModuleSettings::class);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            return [];
        }

        return [
            "NotificationRequestItem" => [
                "additionalData" => [
                    "hmacSignature" => $moduleSettings->getHmacSignature()
                ],
                "amount" => [
                    "currency" => "EUR",
                    "value" => 1000
                ],
                "eventCode" => "CAPTURE",
                "eventDate" => "2021-01-01T01:00:00+01:00",
                "merchantAccountCode" => $moduleSettings->getMerchantAccount(),
                "merchantReference" => "YOUR_MERCHANT_REFERENCE",
                "originalReference" => "9913140798220028",
                "paymentMethod" => "visa",
                "pspReference" => "YOUR_PSP_REFERENCE",
                "reason" => "",
                "success" => "true"
            ]
        ];
    }
}
