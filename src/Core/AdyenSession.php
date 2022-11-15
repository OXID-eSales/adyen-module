<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidEsales\Eshop\Core\Registry;

class AdyenSession
{
    public static function setRedirctLink(string $redirectLink): void
    {
        Registry::getSession()->setVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME, $redirectLink);
    }

    public static function getRedirctLink(): string
    {
        return (string)Registry::getSession()->getVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME);
    }

    public static function deleteRedirctLink(): void
    {
        Registry::getSession()->deleteVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME);
    }
}
