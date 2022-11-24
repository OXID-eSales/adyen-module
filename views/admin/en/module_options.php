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
    'SHOP_MODULE_GROUP_osc_adyen_SANDBOX' => 'Adyen Access Sandbox',
    'SHOP_MODULE_GROUP_osc_adyen_LIVE' => 'Adyen Access Live',
    'SHOP_MODULE_GROUP_osc_adyen_SeperateCapture' => 'Seperate Capture for selected Payments',
    'SHOP_MODULE_GROUP_osc_adyen_Languages' => 'Language-Configuration',
    'SHOP_MODULE_GROUP_osc_adyen_Backend' => 'Backend-Options',

    'SHOP_MODULE_osc_adyen_OperationMode' => 'Operation Mode',
    'SHOP_MODULE_osc_adyen_OperationMode_sandbox' => 'Sandbox',
    'SHOP_MODULE_osc_adyen_OperationMode_live' => 'Live',
    'SHOP_MODULE_osc_adyen_LoggingActive' => 'Logging Active?',

    'SHOP_MODULE_osc_adyen_SandboxAPIKey' => 'API Key',
    'SHOP_MODULE_osc_adyen_SandboxClientKey' => 'Client Key',
    'SHOP_MODULE_osc_adyen_SandboxHmacSignature' => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_osc_adyen_SandboxMerchantAccount' => 'Merchant Account',
    'SHOP_MODULE_osc_adyen_SandboxNotificationUsername' => 'Notification Username',
    'SHOP_MODULE_osc_adyen_SandboxNotificationPassword' => 'Notification Password',
    'SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'PayPal Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'The Live-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    'SHOP_MODULE_osc_adyen_LiveAPIKey' => 'API Key',
    'SHOP_MODULE_osc_adyen_LiveClientKey' => 'Client Key',
    'SHOP_MODULE_osc_adyen_LiveHmacSignature' => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_osc_adyen_LiveMerchantAccount' => 'Merchant Account',
    'SHOP_MODULE_osc_adyen_LiveNotificationUsername' => 'Notification Username',
    'SHOP_MODULE_osc_adyen_LiveNotificationPassword' => 'Notification Password',
    'SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'PayPal Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'The Sandbox-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    'SHOP_MODULE_osc_adyen_SeperateCapture_' . Module::PAYMENT_CREDITCARD_ID => Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['de']['desc'],
    'SHOP_MODULE_osc_adyen_SeperateCapture_' . Module::PAYMENT_PAYPAL_ID => Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['de']['desc'],

    'SHOP_MODULE_osc_adyen_Languages' => 'Language localizations matching the OXID language',
    'HELP_SHOP_MODULE_osc_adyen_Languages' => 'Enter a suitable language and region localization (ISO 639-1 alpha-2 / ISO 3166-1 alpha-2) for each OXID language (Master Settings > Languages > Language > Language abbreviation). One line for each setting (e.g. en => en_US)',
];