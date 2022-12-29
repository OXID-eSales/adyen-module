<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidSolutionCatalysts\Adyen\Exception\Redirect;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use OxidSolutionCatalysts\Adyen\Traits\UserAddress;

class OrderController extends OrderController_parent
{
    use ServiceContainer;
    use UserAddress;

    /**
     * @inheritDoc
     *
     * @param integer $success status code
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function _getNextStep($success) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $session = $this->getServiceFromContainer(SessionSettings::class);
        $redirectLink = $session->getRedirctLink();
        if (
            (Order::ORDER_STATE_ADYENPAYMENTNEEDSREDICRET == $success) &&
            $redirectLink
        ) {
            $session->deleteRedirctLink();
            throw new Redirect($redirectLink);
        }
        return parent::_getNextStep($success);
    }
}
