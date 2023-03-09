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
$keyDelayKlarna = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_KLARNA_ID;
$descKlarna = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_ID]['descriptions']['de']['desc'];
$keyDelayTwint = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_TWINT_ID;
$descTwint = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_TWINT_ID]['descriptions']['de']['desc'];

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
    'SHOP_MODULE_osc_adyen_OperationMode_sandbox' => 'Sandbox',
    'SHOP_MODULE_osc_adyen_OperationMode_live' => 'Live',
    'SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'Protokollierung aktiv?',
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics aktiv?',

    'SHOP_MODULE_osc_adyen_SandboxAPIKey' => 'API Schlüssel',
    'SHOP_MODULE_osc_adyen_SandboxClientKey' => 'Client Schlüssel',
    'SHOP_MODULE_osc_adyen_SandboxHmacSignature' => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_osc_adyen_SandboxMerchantAccount' => 'Shopbetreiber Konto',
    'SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'PayPal Merchant Id',
    'SHOP_MODULE_osc_adyen_SandboxGooglePayMerchantId' => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_SandboxGooglePayMerchantId' => 'Die Live-"Google Pay Merchant Id" wird nur benötigt, wenn Sie Google Pay über Adyen nutzen wollen',
    'HELP_SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'Die Live-"PayPal Merchant Id" wird nur benötigt, wenn Sie PayPal über Adyen nutzen wollen',

    'SHOP_MODULE_osc_adyen_LiveAPIKey' => 'API Schlüssel',
    'SHOP_MODULE_osc_adyen_LiveClientKey' => 'Client Schlüssel',
    'SHOP_MODULE_osc_adyen_LiveHmacSignature' => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_osc_adyen_LiveMerchantAccount' => 'Shopbetreiber Konto',
    'SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'PayPal Merchant Id',
    'SHOP_MODULE_osc_adyen_LiveGooglePayMerchantId' => 'Google Pay Merchant Id',

    'SHOP_MODULE_osc_adyen_KlarnaPaymentType' => 'Payment Typ',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna' => ' Klarna — Pay later',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna_paynow' => ' Klarna — Pay Now',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna_account' => ' Klarna — Pay Over Time',

    'HELP_SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'Die Sandbox-"PayPal Merchant Id" wird nur benötigt, wenn Sie PayPal über Adyen nutzen wollen',
    'SHOP_MODULE_osc_adyen_LiveEndpointPrefix' => 'Live Endpoint Prefix',
    'HELP_SHOP_MODULE_osc_adyen_LiveEndpointPrefix' => 'Im Live-System wir für jeden Merchant ein Prefix gebraucht. Der Prefix wird in Adyen definiert. (siehe: https://docs.adyen.com/development-resources/live-endpoints)',
    'SHOP_MODULE_osc_adyen_Languages' => 'Sprachlokalisierungen passend zur OXID-Sprache',
    'HELP_SHOP_MODULE_osc_adyen_Languages' =>
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
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayKlarna, $descKlarna),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationDEArrayForOption($keyDelayTwint, $descTwint),
);