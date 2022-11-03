<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class AdyenWebhookController
 */
class AdyenWebhookController extends WidgetController
{
    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        try {
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
}
