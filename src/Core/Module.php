<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

final class Module
{
    public const MODULE_NAME_DE = 'Adyen Payment für OXID';
    public const MODULE_NAME_EN = 'Adyen Payment for OXID';
    public const MODULE_VERSION = '1.0.0-rc.1';
    public const MODULE_VERSION_FULL = self::MODULE_VERSION . ' SDK-Version ' . self::ADYEN_SDK_VERSION;

    public const ADYEN_SDK_VERSION = '5.27.0';
    public const ADYEN_INTEGRITY_JS = 'sha384-YGWSKjvKe65KQJXrOTMIv0OwvG+gpahBNej9I3iVl4eMXhdUZDUwnaQdsNV5OCWp';
    public const ADYEN_INTEGRITY_CSS = 'sha384-2MpA/pwUY9GwUN1/eXoQL3SDsNMBV47TIywN1r5tb8JB4Shi7y5dyRZ7AwDsCnP8';

    public const MODULE_ID = 'osc_adyen';

    public const PAYMENT_CREDITCARD_ID = 'oscadyencreditcard';
    public const PAYMENT_PAYPAL_ID = 'oscadyenpaypal';

    public const ADYEN_HISTORY_TABLE = 'oscadyenhistory';

    public const ADYEN_CAPTURE_DELAY_MANUAL = 'manual';
    public const ADYEN_CAPTURE_DELAY_IMMEDIATE = 'immediate';
    public const ADYEN_CAPTURE_DELAY_DAYS = 'days';

    public const ADYEN_ACTION_AUTHORIZE = 'authorize';
    public const ADYEN_ACTION_CAPTURE = 'capture';
    public const ADYEN_ACTION_REFUND = 'refund';
    public const ADYEN_ACTION_CANCEL = 'cancel';

    public const ADYEN_STATUS_AUTHORISED = 'authorised';
    public const ADYEN_STATUS_CANCELLED = 'cancelled';
    public const ADYEN_STATUS_CAPTURED = 'captured';
    public const ADYEN_STATUS_CAPTUREFAILED = 'capturefailed';
    public const ADYEN_STATUS_ERROR = 'error';
    public const ADYEN_STATUS_EXPIRED = 'expired';
    public const ADYEN_STATUS_RECEIVED = 'received';
    public const ADYEN_STATUS_REFUSED = 'refused';
    public const ADYEN_STATUS_SENTFORSETTLE = 'sentforsettle';
    public const ADYEN_STATUS_SETTLESCHEDULED = 'settlescheduled';
    public const ADYEN_STATUS_SETTLED = 'settled';
    public const ADYEN_STATUS_CHARGEBACK = 'chargeback';
    public const ADYEN_STATUS_REFUNDED = 'refunded';
    public const ADYEN_STATUS_REFUNDFAILED = 'refundfailed';
    public const ADYEN_STATUS_REFUNDEDREVERSED = 'refundedreversed';
    public const ADYEN_STATUS_REFUNDSCHEDULED = 'refundscheduled';
    public const ADYEN_STATUS_SENTFORREFUND = 'sentforrefund';

    public const ADYEN_HTMLPARAM_PAYMENTSTATEDATA_NAME = 'adyenStateDataPaymentMethod';

    private const PAYMENT_CONSTRAINTS = [
        'oxfromamount' => 0.01,
        'oxtoamount' => 60000,
        'oxaddsumtype' => 'abs'
    ];

    public const PAYMENT_DEFINTIONS = [

        //Creditcard
        self::PAYMENT_CREDITCARD_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Kreditkarte',
                    'longdesc' => 'Kreditkarte',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Kreditkarte.'
                ],
                'en' => [
                    'desc' => 'Creditcard',
                    'longdesc' => 'Creditcard',
                    'longdesc_beta' => 'Pay conveniently with Creditcard.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'capturedelay' => true,
            'paymentCtrl' => true
        ],
        self::PAYMENT_PAYPAL_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'PayPal',
                    'longdesc' => 'PayPal',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit PayPal.'
                ],
                'en' => [
                    'desc' => 'PayPal',
                    'longdesc' => 'PayPal',
                    'longdesc_beta' => 'Pay conveniently with PayPal.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'capturedelay' => true,
            'paymentCtrl' => false
        ]
    ];

    public static function isAdyenPayment(string $paymentId): bool
    {
        return (isset(self::PAYMENT_DEFINTIONS[$paymentId]));
    }

    public static function showInPaymentCtrl(string $paymentId): bool
    {
        return (self::isAdyenPayment($paymentId) &&
            self::PAYMENT_DEFINTIONS[$paymentId]['paymentCtrl']);
    }

    public static function isCaptureDelay(string $paymentId): bool
    {
        return (self::isAdyenPayment($paymentId) &&
            self::PAYMENT_DEFINTIONS[$paymentId]['capturedelay']); /* @phpstan-ignore-line */
    }
}
