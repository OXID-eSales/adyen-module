<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

class_alias(
    \OxidEsales\Eshop\Application\Model\Order::class,
    \OxidSolutionCatalysts\Adyen\Model\Order_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Model\Basket::class,
    \OxidSolutionCatalysts\Adyen\Model\Basket_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Model\Payment::class,
    \OxidSolutionCatalysts\Adyen\Model\Payment_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Model\PaymentGateway::class,
    \OxidSolutionCatalysts\Adyen\Model\PaymentGateway_parent::class
);

class_alias(
    \OxidEsales\Eshop\Core\ViewConfig::class,
    \OxidSolutionCatalysts\Adyen\Core\ViewConfig_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class,
    \OxidSolutionCatalysts\Adyen\Controller\Admin\OrderList_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Controller\OrderController::class,
    \OxidSolutionCatalysts\Adyen\Controller\OrderController_parent::class
);

class_alias(
    \OxidEsales\Eshop\Application\Controller\PaymentController::class,
    \OxidSolutionCatalysts\Adyen\Controller\PaymentController_parent::class
);
