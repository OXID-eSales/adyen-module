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
    'OSC_ADYEN_HISTORY' => 'Adyen Order-History',
    'OSC_ADYEN_ACTION'  => 'Adyen Action',

    'OSC_ADYEN_CAPTUREMONEY' => 'Capture Money',
    'OSC_ADYEN_CAPTURE' => 'capture',

    'OSC_ADYEN_REFUNDMONEY' => 'Refund Money',
    'OSC_ADYEN_REFUND' => 'refund',

    'OSC_ADYEN_CANCELORDER' => 'Cancel Order',
    'OSC_ADYEN_CANCEL' => 'cancel',

    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_AUTHORIZE => 'Authorization',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CAPTURE => 'Capture',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_REFUND => 'Refund',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CANCEL => 'Cancel',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_AUTHORISED => 'Authorised',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CANCELLED => 'Cancelled',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CAPTURED => 'Captured',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CAPTUREFAILED => 'CaptureFailed',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_ERROR => 'Error',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_EXPIRED => 'Expired',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_RECEIVED => 'Received',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUSED => 'Refused',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SENTFORSETTLE => 'SentForSettle',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SETTLESCHEDULED => 'SettleScheduled',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SETTLED => 'Settled',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CHARGEBACK => 'Chargeback',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDED => 'Refunded',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDFAILED => 'RefundFailed',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDEDREVERSED => 'RefundedReversed',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDSCHEDULED => 'RefundScheduled',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SENTFORREFUND => 'SentForRefund',

    // extend order_list.tpl
    'ORDER_SEARCH_FIELD_PSPREFERENCE' => 'Adyen PSP Reference',

    // extend module_config.tpl
    'OSC_ADYEN_CONFIG_HEAD' => 'important Module-informations',
    'OSC_ADYEN_CONFIG_OPTIONS' => 'Module-options',
    'OSC_ADYEN_CONFIG_SDK' => 'used SDK-Version',
    'OSC_ADYEN_CONFIG_WEBHOOKURL' => 'Webhook-Url (Please copy and store in the Adyen backend)',
    'OSC_ADYEN_CONFIG_HEALTH_CONFIG' => 'configuration complete',
    'OSC_ADYEN_CONFIG_HEALTH_ADYEN' => 'Payment methods available in Adyen-Admin',
];
