<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Core\Webhook\Handler;

use Doctrine\DBAL\Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Handler\AuthorizationHandler;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class AuthorizationHandlerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $order = oxNew(Order::class);
        $order->setAdyenPSPReference("YOUR_PSP_REFERENCE");
        $order->assign([
            'oxorder__oxpaymenttype' => Module::PAYMENT_CREDITCARD_ID
        ]);
        $order->save();

        $adyenHistory = oxNew(AdyenHistory::class);
        $adyenHistory->setOrderId($order->getId());
        $adyenHistory->setShopId(Registry::getConfig()->getShopId());
        $adyenHistory->setPrice(1000);
        $adyenHistory->setTimeStamp("2021-01-01 01:00:00");
        $adyenHistory->setPSPReference("YOUR_PSP_REFERENCE");
        $adyenHistory->setAdyenStatus("AUTHORIZATION");
        $adyenHistory->setAdyenAction(Module::ADYEN_ACTION_AUTHORIZE);
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

    /**
     * @throws WebhookEventTypeException
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function testUpdateStatus()
    {
        $event = oxNew(Event::class, $this->proceedNotificationData());

        $authorizationHandler = oxNew(AuthorizationHandler::class);
        $authorizationHandler->setData($event);
        $authorizationHandler->updateStatus($event);

        $historyList = oxNew(AdyenHistoryList::class);
        $orderId = $historyList->getOxidOrderIdByPSPReference("YOUR_PSP_REFERENCE");

        $this->assertNotNull($orderId);

        $order = oxNew(Order::class);
        $order->load($orderId);

        $this->assertNotNull($order);
    }

    public function testHandle()
    {
        $event = oxNew(Event::class, $this->proceedNotificationData());

        $authorizationHandler = oxNew(AuthorizationHandler::class);
        $authorizationHandler->handle($event);

        $historyList = oxNew(AdyenHistoryList::class);
        $historyList->init(AdyenHistory::class);
        $orderId = $historyList->getOxidOrderIdByPSPReference("YOUR_PSP_REFERENCE");

        $this->assertNotNull($orderId);

        $order = oxNew(Order::class);
        $order->load($orderId);

        $this->assertNotNull($order);
    }

    private function proceedNotificationData()
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
            "live" => "false",
            "notificationItems" => [
                [
                    "NotificationRequestItem" => [
                        "additionalData" => [
                            "recurring.recurringDetailReference" => "9915692881181044",
                            "recurring.shopperReference" => "YOUR_SHOPPER_REFERENCE",
                            "hmacSignature" => '695BB571079C6553880542A611FD36EF2F962EBE56FED0B9E887093296E83DF4'
                        ],
                        "amount" => [
                            "currency" => "EUR",
                            "value" => 1000
                        ],
                        "eventCode" => "AUTHORIZATION",
                        "eventDate" => "2021-01-01T01:00:00+01:00",
                        "merchantAccountCode" => $moduleSettings->getMerchantAccount(),
                        "merchantReference" => "YOUR_MERCHANT_REFERENCE",
                        "paymentMethod" => "ach",
                        "pspReference" => "YOUR_PSP_REFERENCE",
                        "reason" => "null",
                        "success" => "true"
                    ]
                ]
            ]
        ];
    }
}
