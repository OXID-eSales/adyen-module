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
    use UserAddress;
}
