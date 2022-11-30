<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller;

use Doctrine\DBAL\Exception;
use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\Adyen\Core\Response;
use OxidSolutionCatalysts\Adyen\Core\Webhook\Event;
use OxidSolutionCatalysts\Adyen\Core\Webhook\EventDispatcher;
use OxidSolutionCatalysts\Adyen\Exception\WebhookEventException;

/**
 * Class AdyenWebhookController
 */
class AdyenWebhookController extends WidgetController
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function init(): void
    {
        parent::init();

        try {
            $request = file_get_contents('php://input');
            if (!is_string($request)) {
                throw WebhookEventException::dataNotFound();
            }

            $data = json_decode($request, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw WebhookEventException::dataNotFound();
            }

            $eventType = $data["notificationItems"][0]["NotificationRequestItem"]["eventCode"];
            $event = new Event($data, $eventType);

            $eventDispatcher = oxNew(EventDispatcher::class);
            $eventDispatcher->dispatch($event);

            $this->sendAccceptedResponse();
        } catch (\Exception $exception) {
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
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    private function sendErrorResponse(): void
    {
        header('Content-Type: text/html', true, 500);
        // TODO: A hard exit is ok here for now. If this changes, please remove the SuppressWarnings
        exit();
    }

    private function sendAccceptedResponse(): void
    {
        $response = oxNew(Response::class);
        $response->setData([
            "notificationResponse" => "[accepted]"
        ]);
        $response->sendJson();
    }
}
