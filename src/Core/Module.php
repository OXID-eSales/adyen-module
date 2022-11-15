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

    public const ADYEN_ORDER_REFERENCE_ID = 'OXID_REFERENCE';

    public const ADYEN_SESSION_ID_NAME = 'sess_adyen_id';
    public const ADYEN_SESSION_DATA_NAME = 'sess_adyen_session_data';
    public const ADYEN_SESSION_PAYMENTMETHODS_NAME = 'sess_adyen_payment_methods';
    public const ADYEN_SESSION_PAYMENTSTATEDATA_NAME = 'sess_adyen_paymentstatedata';
    public const ADYEN_SESSION_REDIRECTLINK_NAME = 'sess_adyen_redirectlink';

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
            'seperatecapture' => true
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
            'seperatecapture' => true
        ]
    ];

    public static function isAdyenPayment(string $paymentId): bool
    {
        return (isset(self::PAYMENT_DEFINTIONS[$paymentId]));
    }

    public static function isSeperateCapture(string $paymentId): bool
    {
        return (self::isAdyenPayment($paymentId) &&
            self::PAYMENT_DEFINTIONS[$paymentId]['seperatecapture']); /* @phpstan-ignore-line */
    }
}
