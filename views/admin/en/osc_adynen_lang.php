<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidSolutionCatalysts\Adyen\Core\Module;

$sLangName = 'English';

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------
$aLang = [
    'charset' => 'UTF-8',
    'tbclorder_adyen' => 'Adyen',

    // osc_adyen_order.tpl
    'OSC_ADYEN_NO_ADYEN_ORDER' => 'This is no Adyen-Order',
    'OSC_ADYEN_PSPREFERENCE' => 'PSP Reference',
    'OSC_ADYEN_PARENTPSPREFERENCE' => 'Parent PSP Reference',
    'OSC_ADYEN_TIMESTAMP' => 'Timestamp',
    'OSC_ADYEN_STATUS' => 'Adyen Status',
    'OSC_ADYEN_CAPTUREMONEY' => 'Capture Money',
    'OSC_ADYEN_CAPTURE' => 'capture',
    'OSC_ADYEN_REFUNDMONEY' => 'Refund Money',
    'OSC_ADYEN_REFUND' => 'refund',
    'OSC_ADYEN_HISTORY' => 'Adyen Order-History',
    'OSC_ADYEN_ACTION'  => 'Adyen Action',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_AUTHORIZE => 'Authorization',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CAPTURE => 'Capture',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_REFUND => 'Refund',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CANCEL => 'Cancel',

    // extend order_list.tpl
    'ORDER_SEARCH_FIELD_PSPREFERENCE' => 'Adyen PSP Reference',

    // extend module_config.tpl
    'OSC_ADYEN_CONFIG_HEAD' => 'important Module-informations',
    'OSC_ADYEN_CONFIG_OPTIONS' => 'Module-options',
    'OSC_ADYEN_CONFIG_SDK' => 'used SDK-Version',
    'OSC_ADYEN_CONFIG_WEBHOOKURL' => 'Webhook-Url (Please copy and store in the Adyen backend)',
];
