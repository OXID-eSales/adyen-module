<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
 */
class Order extends Order_parent
{
    protected const PSPREFERENCEFIELD = 'adyenpspreference';

    protected ?string $adyenPaymentName = null;

    public function isAdyenOrder(): bool
    {
        return (
            $this->getFieldData('oxpaymenttype') === Module::PAYMENT_CREDITCARD_ID &&
            $this->getAdyenPSPReference()
        );
    }

    public function getAdyenPaymentName(): string
    {
        if (is_null($this->adyenPaymentName)) {
            $paymentId = $this->getFieldData('oxpaymenttype');
            $payment = oxNew(Payment::class);
            $payment->load($paymentId);
            $this->adyenPaymentName = $payment->getFieldData('oxdesc');
        }
        return $this->adyenPaymentName;
    }

    public function getAdyenPSPReference(): string
    {
        return (string) $this->getFieldData(self::PSPREFERENCEFIELD);
    }

    public function setAdyenPSPReference(string $pspReference): void
    {
        $this->assign(
            [
                self::PSPREFERENCEFIELD => $pspReference
            ]
        );
    }
}
