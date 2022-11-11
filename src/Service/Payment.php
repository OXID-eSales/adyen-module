<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidEsales\Eshop\Core\Session;

/**
 * @extendable-class
 */
class Payment
{
    /**
     * @var Session
     */
    private Session $session;


    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Return the PaymentId from session basket
     */
    public function getSessionPaymentId(): string
    {
        return $this->session->getBasket()->getPaymentId();
    }
}
