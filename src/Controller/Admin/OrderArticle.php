<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller\Admin;

use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\Admin\OrderArticle
 */
class OrderArticle extends OrderArticle_parent
{
    use ServiceContainer;

    public function storno(): void
    {
        parent::storno();
    }
}
