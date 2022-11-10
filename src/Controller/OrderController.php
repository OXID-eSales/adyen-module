<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

class OrderController extends OrderController_parent
{
    /**
     * @inheritDoc
     *
     * @param integer $iSuccess status code
     *
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function _getNextStep(int $success) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $result = parent::_getNextStep($success);
        return $result;

    }
}