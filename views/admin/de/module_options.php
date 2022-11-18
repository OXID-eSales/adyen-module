<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidSolutionCatalysts\Adyen\Core\Module;

$aLang = [
    'charset' => 'UTF-8',
    'SHOP_MODULE_GROUP_osc_adyen_API' => 'Adyen API',
    'SHOP_MODULE_GROUP_osc_adyen_SANDBOX' => 'Adyen Zugangsdaten Sandbox',
    'SHOP_MODULE_GROUP_osc_adyen_LIVE' => 'Adyen Zugangsdaten Live',
    'SHOP_MODULE_GROUP_osc_adyen_SeperateCapture' => 'Separater Zahlungseinzug für ausgewählte Zahlungsarten',
    'SHOP_MODULE_GROUP_osc_adyen_Languages' => 'Sprach-Konfiguration',
    'SHOP_MODULE_GROUP_osc_adyen_Backend' => 'Adminbereich-Einstellungen',

    'SHOP_MODULE_osc_adyen_OperationMode' => 'Betriebsmodus',
    'SHOP_MODULE_osc_adyen_OperationMode_sandbox' => 'Sandbox',
    'SHOP_MODULE_osc_adyen_OperationMode_live' => 'Live',
    'SHOP_MODULE_osc_adyen_LoggingActive' => 'Protokollierung aktiv?',

    'SHOP_MODULE_osc_adyen_SandboxAPIKey' => 'API Schlüssel',
    'SHOP_MODULE_osc_adyen_SandboxClientKey' => 'Client Schlüssel',
    'SHOP_MODULE_osc_adyen_SandboxHmacSignature' => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_osc_adyen_SandboxMerchantAccount' => 'Shopbetreiber Konto',
    'SHOP_MODULE_osc_adyen_SandboxNotificationUsername' => 'Benachrichtigungen Name',
    'SHOP_MODULE_osc_adyen_SandboxNotificationPassword' => 'Benachrichtigungen Passwort',

    'SHOP_MODULE_osc_adyen_LiveAPIKey' => 'API Schlüssel',
    'SHOP_MODULE_osc_adyen_LiveClientKey' => 'Client Schlüssel',
    'SHOP_MODULE_osc_adyen_LiveHmacSignature' => 'HMAC Code (Schlüssel-Hash-Nachrichtenauthentifizierungscode)',
    'SHOP_MODULE_osc_adyen_LiveMerchantAccount' => 'Shopbetreiber Konto',
    'SHOP_MODULE_osc_adyen_LiveNotificationUsername' => 'Benachrichtigungen Name',
    'SHOP_MODULE_osc_adyen_LiveNotificationPassword' => 'Benachrichtigungen Passwort',

    'SHOP_MODULE_osc_adyen_SeperateCapture_' . Module::PAYMENT_CREDITCARD_ID => Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['de']['desc'],
    'SHOP_MODULE_osc_adyen_SeperateCapture_' . Module::PAYMENT_PAYPAL_ID => Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['de']['desc'],

    'SHOP_MODULE_osc_adyen_Languages' => 'Sprachlokalisierungen passend zur OXID-Sprache',
    'HELP_SHOP_MODULE_osc_adyen_Languages' => 'Geben Sie für jede OXID-Sprache (Stammdaten > Sprachen > Sprache > Sprachkürzel) eine passende Sprach- und Regionlokalisierung (ISO 639-1 alpha-2 / ISO 3166-1 alpha-2) an. Für jede Einstellung eine Zeile (z.B. en => en_US)',
];