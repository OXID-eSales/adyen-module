<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Model\ModuleOptionsCaptureDelay;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

$keyDelayCreditCard = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_CREDITCARD_ID;
$descCreditCard = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['de']['desc'];
$keyDelayPayPal = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_PAYPAL_ID;
$descPayPal = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['de']['desc'];
$keyDelayGooglePay = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_GOOGLE_PAY_ID;
$descGooglePay = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_GOOGLE_PAY_ID]['descriptions']['de']['desc'];
$keyDelayKlarnaImmediate = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_KLARNA_IMMEDIATE_ID;
$descKlarnaImmediate = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_IMMEDIATE_ID]['descriptions']['de']['desc'];
$keyDelayKlarnaLater = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_KLARNA_LATER_ID;
$descKlarnaLater = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_LATER_ID]['descriptions']['de']['desc'];
$keyDelayKlarnaOverTime = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_KLARNA_OVER_TIME_ID;
$descKlarnaOverTime = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_OVER_TIME_ID]['descriptions']['de']['desc'];
$keyDelayTwint = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_TWINT_ID;
$descTwint = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_TWINT_ID]['descriptions']['de']['desc'];
$keyDelayApplePay = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_APPLE_PAY_ID;
$descApplePay = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_APPLE_PAY_ID]['descriptions']['de']['desc'];

$aLang = [
    'charset' => 'UTF-8',
    'SHOP_MODULE_GROUP_osc_adyen_API' => 'Adyen API',
    'SHOP_MODULE_GROUP_osc_adyen_SANDBOX' => 'Adyen Zugangsdaten Sandbox',
    'SHOP_MODULE_GROUP_osc_adyen_LIVE' => 'Adyen Zugangsdaten Live',
    'SHOP_MODULE_GROUP_osc_adyen_CaptureDelay' => 'Erfassungsverzögerung für ausgewählte Zahlungsarten',
    'SHOP_MODULE_GROUP_osc_adyen_Languages' => 'Sprach-Konfiguration',
    'SHOP_MODULE_GROUP_osc_adyen_Backend' => 'Adminbereich-Einstellungen',
    'SHOP_MODULE_GROUP_osc_adyen_KLARNA' => 'Klarna Einstellungen',

    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE => 'Betriebsmodus',
    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE . '_sandbox' => 'Sandbox',
    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE . '_live' => 'Live',
    'SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'Protokollierung aktiv?',
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics aktiv?',

    'SHOP_MODULE_' . ModuleSettings::SANDBOX_API_KEY => 'API Schlüssel',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_CLIENT_KEY => 'Client Schlüssel',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_HMAC_SIGNATURE => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_MERCHANT_ACCOUNT => 'Shopbetreiber Konto',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID => 'PayPal Merchant Id',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_GOOGLE_PAY_MERCHANT_ID => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_' . ModuleSettings::SANDBOX_GOOGLE_PAY_MERCHANT_ID => 'Die Live-"Google Pay Merchant Id" wird nur benötigt, wenn Sie Google Pay über Adyen nutzen wollen',
    'HELP_SHOP_MODULE_' . ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID => 'Die Live-"PayPal Merchant Id" wird nur benötigt, wenn Sie PayPal über Adyen nutzen wollen',

    'SHOP_MODULE_' . ModuleSettings::LIVE_API_KEY => 'API Schlüssel',
    'SHOP_MODULE_' . ModuleSettings::LIVE_CLIENT_KEY => 'Client Schlüssel',
    'SHOP_MODULE_' . ModuleSettings::LIVE_HMAC_SIGNATURE => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_' . ModuleSettings::LIVE_MERCHANT_ACCOUNT => 'Shopbetreiber Konto',
    'SHOP_MODULE_' . ModuleSettings::LIVE_PAYPAL_MERCHANT_ID => 'PayPal Merchant Id',
    'SHOP_MODULE_' . ModuleSettings::LIVE_GOOGLE_PAY_MERCHANT_ID => 'Google Pay Merchant Id',

    'HELP_SHOP_MODULE_' . ModuleSettings::LIVE_PAYPAL_MERCHANT_ID => 'Die Sandbox-"PayPal Merchant Id" wird nur benötigt, wenn Sie PayPal über Adyen nutzen wollen',
    'SHOP_MODULE_' . ModuleSettings::LIVE_ENDPOINT_PREFIX => 'Live Endpoint Prefix',
    'HELP_SHOP_MODULE_' . ModuleSettings::LIVE_ENDPOINT_PREFIX => 'Im Live-System wir für jeden Merchant ein Prefix gebraucht. Der Prefix wird in Adyen definiert. (siehe: https://docs.adyen.com/development-resources/live-endpoints)',
    'SHOP_MODULE_' . ModuleSettings::LANGUAGES => 'Sprachlokalisierungen passend zur OXID-Sprache',
    'HELP_SHOP_MODULE_' . ModuleSettings::LANGUAGES =>
        'Geben Sie für jede OXID-Sprache (Stammdaten > Sprachen > Sprache > Sprachkürzel) eine passende Sprach- und Regionlokalisierung
        (ISO 639-1 alpha-2 / ISO 3166-1 alpha-2) an. Für jede Einstellung eine Zeile (z.B. en => en_US)',
];
$moduleOptionsCaptureDelay = new ModuleOptionsCaptureDelay();

$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayCreditCard, $descCreditCard),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayPayPal, $descPayPal),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayGooglePay, $descGooglePay),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayKlarnaImmediate, $descKlarnaImmediate),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayKlarnaLater, $descKlarnaLater),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayKlarnaOverTime, $descKlarnaOverTime),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayTwint, $descTwint),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayApplePay, $descApplePay),
);