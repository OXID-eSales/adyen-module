<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

class AdyenSession
{
    use ServiceContainer;

    private static Session $session;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        self::$session = $this->getServiceFromContainer(Session::class);
    }

    public static function setRedirctLink(string $redirectLink): void
    {
        self::$session->setVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME, $redirectLink);
    }

    public static function getRedirctLink(): string
    {
        return self::$session->getVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME);
    }

    public static function deleteRedirctLink(): void
    {
        self::$session->deleteVariable(Module::ADYEN_SESSION_REDIRECTLINK_NAME);
    }
}
