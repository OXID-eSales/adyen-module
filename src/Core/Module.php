<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

final class Module
{
    public const MODULE_ID = 'osc_adyen';

    public const PAYMENT_CREDITCARD_ID = 'oscadyencreditcard';

    public const ADYEN_HISTORY_TABLE = 'oscadyenhistory';

    public const ADYEN_ORDER_REFERENCE_ID = 'OXID_REFERENCE';

    public const ADYEN_SESSION_ID_NAME = 'sess_adyen_id';
    public const ADYEN_SESSION_DATA_NAME = 'sess_adyen_session_data';

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
                    'desc' => 'Adyen',
                    'longdesc' => 'Adyen',
                    'longdesc_beta' => 'Bezahlen Sie bequem mit Adyen.'
                ],
                'en' => [
                    'desc' => 'Adyen',
                    'longdesc' => 'Adyen',
                    'longdesc_beta' => 'Pay conveniently with Adyen.'
                ]
            ],
            'countries' => [],
            'currencies' => [],
            'constraints' => self::PAYMENT_CONSTRAINTS
        ]
    ];

    public static function isAdyenPayment(string $paymentId): bool
    {
        return (isset(self::PAYMENT_DEFINTIONS[$paymentId]));
    }
}
