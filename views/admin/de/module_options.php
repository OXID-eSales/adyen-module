<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

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
    'HELP_SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'Bei Aktivierung werden alle API-Calls in eine Log-Datei (.../log/adyen/...) geschrieben. Die Informationen sind hilfreich für den Support und das Debugging.',
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics aktiv?',
    'HELP_SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Bei Aktivierung werden Analyse-Daten während des Checkouts gesammelt und an Adyen übertragen. Informationen dazu unter: https://docs.adyen.com/online-payments/analytics-and-data-tracking',

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

$paymentLang = [
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_CREDITCARD_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_PAYPAL_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_GOOGLE_PAY_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_GOOGLE_PAY_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_IMMEDIATE_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_IMMEDIATE_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_LATER_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_LATER_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_OVER_TIME_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_OVER_TIME_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_TWINT_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_TWINT_ID]['descriptions']['de']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_APPLE_PAY_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_APPLE_PAY_ID]['descriptions']['de']['desc']
];

foreach ($paymentLang as $key => $description) {
    $key = 'SHOP_MODULE_' . $key;
    $paymentLang = [
        $key => $description,
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manuell',
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Tagen',
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Sofort',
        'HELP_' . $key => 'In Adyen kann man die Verzögerung des Geldeinzugs für '
            . $description . 'definieren: "Immediate", "after n days" oder "Manual".'
            . 'Die Adyen-Einstellung muss mit der vom Shop korrespondieren.'
            . ' Im Fall "Manual" kann unter Bestellungen verwalten > Bestellungen > Reiter Adyen,'
            . ' der Geldeinzug angestoßen werden.'
    ];
    $aLang += $paymentLang;
}