<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;

/**
 * Order class wrapper for Adyen module
 */
class AdminOrderController extends AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'osc_adyen_order.tpl';
}
