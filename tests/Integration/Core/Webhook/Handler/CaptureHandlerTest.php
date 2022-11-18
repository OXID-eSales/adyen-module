<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Core\Webhook\Handler;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
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

        // it is important to have a real (but dummy) HMAC-Signature here for test
        $moduleSettingsBridge = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettingBridgeInterface::class);
        $moduleSettingsBridge->save(
            'osc_adyen_SandboxHmacSignature',
            '5d1e6516f4f21ef0d2d12b163ce5f7c6f6f731da99567afbc83af10c160d8c2d',
            Module::MODULE_ID
        );
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

//$moduleSettings->getHmacSignature()

        return [
            "NotificationRequestItem" => [
                "additionalData" => [
                    "hmacSignature" => '695BB571079C6553880542A611FD36EF2F962EBE56FED0B9E887093296E83DF4'
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