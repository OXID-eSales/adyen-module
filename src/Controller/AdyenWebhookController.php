<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Header;
use OxidSolutionCatalysts\Adyen\Core\Response;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\EventDispatcher;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventException;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventTypeException;
use OxidSolutionCatalysts\Adyen\Traits\Json;

/**
 * Class AdyenWebhookController
 */
class AdyenWebhookController extends WidgetController
{
    use Json;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function init(): void
    {
        parent::init();

        try {
            $request = $this->getJsonPostData();
            if (!is_string($request)) {
                throw WebhookEventException::dataNotFound();
            }
            $data = $this->jsonToArray($this->getJsonPostData());
            if (empty($data)) {
                throw WebhookEventException::dataNotFound();
            }

            $event = new Event($data);

            $eventDispatcher = oxNew(EventDispatcher::class);
            $eventDispatcher->dispatch($event);

            if (!$event->isHMACVerified()) {
                throw WebhookEventException::hmacValidationFailed();
            }

            $this->sendAcceptedResponse();
        } catch (WebhookEventTypeException | \Exception $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            $this->sendErrorResponse();
        }
        //We need to return a 200 if the call could be processed successfully, the otherwise webhook event
        //will be sent it again:
        //  "If your app responds with any other status code, PayPal tries to resend the notification
        //   message 25 times over the course of three days."
        Registry::getUtils()->showMessageAndExit('');
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function sendErrorResponse(): void
    {
        $header = Registry::get(Header::class);
        $header->setHeader('Cache-Control: no-cache');
        $header->setHeader('Content-Type: text/html');
        $header->setHeader('Status: 500');
        Registry::getUtils()->showMessageAndExit('');
    }

    private function sendAcceptedResponse(): void
    {
        $response = oxNew(Response::class);
        $response->setData([
            "notificationResponse" => "[accepted]"
        ]);
        $response->sendJson();
    }
}
