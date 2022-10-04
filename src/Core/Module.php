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

    public const STANDARD_PAYMENT_ID = 'oscadyen';

    public const ADYEN_HISTORY_TABLE = 'oscadyenhistory';

    public const ADYEN_ORDER_REFERENCE_ID = 'OXID_REFERENCE';

    private const PAYMENT_CONSTRAINTS = [
        'oxfromamount' => 0.01,
        'oxtoamount' => 60000,
        'oxaddsumtype' => 'abs'
    ];

    public const PAYMENT_DEFINTIONS = [

        //Standard Adyen
        self::STANDARD_PAYMENT_ID => [
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
}
