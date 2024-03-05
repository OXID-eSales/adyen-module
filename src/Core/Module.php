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
    public const MODULE_VERSION = '1.1.5';
    public const MODULE_VERSION_FULL = self::MODULE_VERSION . ' SDK-Version ' . self::ADYEN_SDK_VERSION;
    public const MODULE_PLATFORM_NAME = 'OXID';
    public const MODULE_PLATFORM_VERSION = '1.0';
    public const MODULE_PLATFORM_INTEGRATOR = 'OSC';
    public const ADYEN_SDK_VERSION = '5.27.0';
    public const ADYEN_INTEGRITY_JS = 'sha384-YGWSKjvKe65KQJXrOTMIv0OwvG+gpahBNej9I3iVl4eMXhdUZDUwnaQdsNV5OCWp';
    public const ADYEN_INTEGRITY_CSS = 'sha384-2MpA/pwUY9GwUN1/eXoQL3SDsNMBV47TIywN1r5tb8JB4Shi7y5dyRZ7AwDsCnP8';

    public const MODULE_ID = 'osc_adyen';

    public const PAYMENT_CREDITCARD_ID = 'oscadyencreditcard';
    public const PAYMENT_PAYPAL_ID = 'oscadyenpaypal';
    public const PAYMENT_GOOGLE_PAY_ID = 'oscadyengooglepay';

    public const PAYMENT_KLARNA_LATER_ID = 'oscadyenklarna';
    public const PAYMENT_KLARNA_IMMEDIATE_ID = 'oscadyenklarnapaynow';
    public const PAYMENT_KLARNA_OVER_TIME_ID = 'oscadyenklarnaaccount';

    public const PAYMENT_TWINT_ID = 'oscadyentwint';

    public const PAYMENT_APPLE_PAY_ID = 'oscadyenapplepay';

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
    public const ADYEN_STATUS_CAPTURE_FAILED = 'capturefailed';
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
    public const ADYEN_RETURN_RESULT_CODE_AUTHORISED = 'Authorised';
    public const ADYEN_RETURN_RESULT_CODE_RECEIVED = 'Received';
    public const ADYEN_RETURN_RESULT_CODE_CANCELLED = 'Cancelled';
    public const ADYEN_RETURN_RESULT_CODE_REFUSED = 'Refused';
    public const ADYEN_RETURN_RESULT_CODE_ERROR = 'Error';
    public const ADYEN_HTMLPARAM_PSPREFERENCE_NAME = 'adyenPspReference';
    public const ADYEN_HTMLPARAM_RESULTCODE_NAME = 'adyenResultCode';
    public const ADYEN_HTMLPARAM_AMOUNTCURRENCY_NAME = 'adyenAmountCurrency';
    public const ADYEN_HTMLPARAM_AMOUNTVALUE_NAME = 'adyenAmountValue';
    public const ADYEN_ERROR_INVALIDSESSION_NAME = 'invalidAdyenSession';

    public const PAYMENT_CONSTRAINTS = [
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
            'sort' => -1,
            'capturedelay' => true,
            'paymentCtrl' => true,
            'handleAssets' => true,
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
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
        ],
        self::PAYMENT_GOOGLE_PAY_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Google Pay',
                    'longdesc' => 'Google Pay',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Google Pay.'
                ],
                'en' => [
                    'desc' => 'Google Pay',
                    'longdesc' => 'Google Pay',
                    'longdesc_beta' => 'Pay conveniently with Google Pay.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
        ],
        self::PAYMENT_KLARNA_LATER_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Klarna',
                    'longdesc' => 'Klarna',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Klarna.'
                ],
                'en' => [
                    'desc' => 'Klarna',
                    'longdesc' => 'Klarna',
                    'longdesc_beta' => 'Pay conveniently with Klarna.',
                ],
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
        ],
        self::PAYMENT_KLARNA_OVER_TIME_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Klarna Ratenzahlung',
                    'longdesc' => 'Klarna Ratenzahlung',
                    'longdesc_beta' => 'Bezahlen Sie bequem in Raten Klarna.',
                ],
                'en' => [
                    'desc' => 'Klarna Installment',
                    'longdesc' => 'Klarna Installment',
                    'longdesc_beta' => 'Pay conveniently in installments with Klarna.',
                ],
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
        ],
        self::PAYMENT_KLARNA_IMMEDIATE_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Klarna Sofortbezahlung',
                    'longdesc' => 'Klarna Sofortbezahlung',
                    'longdesc_beta' => 'Bezahlen Sie sofort mit Klarna.'
                ],
                'en' => [
                    'desc' => 'Klarna Immediate',
                    'longdesc' => 'Klarna Immediate',
                    'longdesc_beta' => 'Pay immediately with Klarna.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
        ],
        self::PAYMENT_TWINT_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Twint',
                    'longdesc' => 'Twint',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Twint.'
                ],
                'en' => [
                    'desc' => 'Twint',
                    'longdesc' => 'Twint',
                    'longdesc_beta' => 'Pay conveniently with Twint.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => false,
            'supported_currencies' => ['CHF'],
        ],
        self::PAYMENT_APPLE_PAY_ID => [
            'descriptions' => [
                'de' => [
                    'desc' => 'Apple Pay',
                    'longdesc' => 'Apple Pay',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Apple Pay.'
                ],
                'en' => [
                    'desc' => 'Apple Pay',
                    'longdesc' => 'Apple Pay',
                    'longdesc_beta' => 'Pay conveniently with Apple Pay.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS,
            'sort' => 0,
            'capturedelay' => true,
            'paymentCtrl' => false,
            'handleAssets' => true,
        ],
    ];
}
