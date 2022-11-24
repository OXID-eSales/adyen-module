<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidSolutionCatalysts\Adyen\Core\Module;

$sLangName = 'Deutsch';

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------
$aLang = [
    'charset' => 'UTF-8',
    'tbclorder_adyen' => 'Adyen',

    // osc_adyen_order.tpl
    'OSC_ADYEN_NO_ADYEN_ORDER' => 'Das ist keine Adyen-Bestellung',
    'OSC_ADYEN_PSPREFERENCE' => 'PSP Referenz',
    'OSC_ADYEN_PARENTPSPREFERENCE' => 'Haupt PSP Referenz',
    'OSC_ADYEN_TIMESTAMP' => 'Zeitstempel',
    'OSC_ADYEN_STATUS' => 'Adyen Status',
    'OSC_ADYEN_HISTORY' => 'Adyen Bestell-Historie',
    'OSC_ADYEN_ACTION'  => 'Adyen Aktion',

    'OSC_ADYEN_CAPTUREMONEY' => 'Geldeinzug',
    'OSC_ADYEN_CAPTURE' => 'einziehen',

    'OSC_ADYEN_REFUNDMONEY' => 'Gelderstattung',
    'OSC_ADYEN_REFUND' => 'erstatten',

    'OSC_ADYEN_CANCELORDER' => 'Bestellung stornieren',
    'OSC_ADYEN_CANCEL' => 'stornieren',

    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_AUTHORIZE => 'Authorisierung',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CAPTURE => 'Geldeinzug',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_REFUND => 'Erstattung',
    'OSC_ADYEN_ACTION' . Module::ADYEN_ACTION_CANCEL => 'Abbruch',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_AUTHORISED => 'Autorisiert',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CANCELLED => 'Storniert',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CAPTUREFAILED => 'Erfassung fehlgeschlagen',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_ERROR => 'Fehler',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_EXPIRED => 'Abgelaufen',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_RECEIVED => 'Empfangen',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUSED => 'Abgelehnt',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SENTFORSETTLE => 'Zur Abrechnung geschickt',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SETTLESCHEDULED => 'Abrechnung geplant',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SETTLED => 'Abgerechnet',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_CHARGEBACK => 'Rückbuchung',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDED => 'Erstattet',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDFAILED => 'Rückerstattung fehlgeschlagen',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDEDREVERSED => 'Rückerstattet storniert',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_REFUNDSCHEDULED => 'Rückerstattung geplant',
    'OSC_ADYEN_STATUS' . Module::ADYEN_STATUS_SENTFORREFUND => 'Zur Rückerstattung gesendet',

    // extend order_list.tpl
    'ORDER_SEARCH_FIELD_PSPREFERENCE' => 'Adyen PSP Referenz',

    // extend module_config.tpl
    'OSC_ADYEN_CONFIG_HEAD' => 'wichtige Modul-Informationen',
    'OSC_ADYEN_CONFIG_OPTIONS' => 'Modul-Einstellungen',
    'OSC_ADYEN_CONFIG_SDK' => 'genutzte SDK-Version',
    'OSC_ADYEN_CONFIG_WEBHOOKURL' => 'Webhook-Url (Bitte kopieren und im Adyen-Backend hinterlegen)',
];
