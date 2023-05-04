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
    'SHOP_MODULE_GROUP_osc_adyen_SANDBOX' => 'Adyen Access Sandbox',
    'SHOP_MODULE_GROUP_osc_adyen_LIVE' => 'Adyen Access Live',
    'SHOP_MODULE_GROUP_osc_adyen_CaptureDelay' => 'Capture delay for selected Payments',
    'SHOP_MODULE_GROUP_osc_adyen_Languages' => 'Language-Configuration',
    'SHOP_MODULE_GROUP_osc_adyen_Backend' => 'Backend-Options',
    'SHOP_MODULE_GROUP_osc_adyen_KLARNA' => 'Klarna Settings',

    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE => 'Operation Mode',
    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE . '_sandbox' => 'Sandbox',
    'SHOP_MODULE_' . ModuleSettings::OPERATION_MODE . '_live' => 'Live',
    'SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'Logging active?',
    'HELP_SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'If activated, all API calls are written to a log file (.../log/adyen/...). The information is helpful for support and debugging.',
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics active?',
    'HELP_SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'If activated, analysis data is collected during checkout and transmitted to Adyen. Information on this at: https://docs.adyen.com/online-payments/analytics-and-data-tracking',

    'SHOP_MODULE_' . ModuleSettings::SANDBOX_API_KEY => 'API Key',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_CLIENT_KEY => 'Client Key',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_HMAC_SIGNATURE => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_MERCHANT_ACCOUNT => 'Merchant Account',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID => 'PayPal Merchant Id',
    'SHOP_MODULE_' . ModuleSettings::SANDBOX_GOOGLE_PAY_MERCHANT_ID => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_' . ModuleSettings::SANDBOX_GOOGLE_PAY_MERCHANT_ID => 'The Live-"Google Pay Merchant Id" is only required if you want to use Google Pay via Adyen',
    'HELP_SHOP_MODULE_' . ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID => 'The Live-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    'SHOP_MODULE_' . ModuleSettings::LIVE_API_KEY => 'API Key',
    'SHOP_MODULE_' . ModuleSettings::LIVE_CLIENT_KEY => 'Client Key',
    'SHOP_MODULE_' . ModuleSettings::LIVE_HMAC_SIGNATURE => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_' . ModuleSettings::LIVE_MERCHANT_ACCOUNT => 'Merchant Account',
    'SHOP_MODULE_' . ModuleSettings::LIVE_PAYPAL_MERCHANT_ID => 'PayPal Merchant Id',
    'SHOP_MODULE_' . ModuleSettings::LIVE_GOOGLE_PAY_MERCHANT_ID => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_' . ModuleSettings::LIVE_PAYPAL_MERCHANT_ID => 'The Sandbox-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',
    'SHOP_MODULE_' . ModuleSettings::LIVE_ENDPOINT_PREFIX => 'Live Endpoint Prefix',
    'HELP_SHOP_MODULE_' . ModuleSettings::LIVE_ENDPOINT_PREFIX => 'In the live system, a prefix is needed for each merchant. The prefix is defined in Adyen. (check: https://docs.adyen.com/development-resources/live-endpoints)',
    'SHOP_MODULE_' . ModuleSettings::LANGUAGES => 'Language localizations matching the OXID language',
    'HELP_SHOP_MODULE_' . ModuleSettings::LANGUAGES =>
        'Enter a suitable language and region localization (ISO 639-1 alpha-2 / ISO 3166-1 alpha-2) for each OXID language
        (Master Settings > Languages > Language > Language abbreviation). One line for each setting (e.g. en => en_US)',
];

$paymentLang = [
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_CREDITCARD_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_PAYPAL_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_GOOGLE_PAY_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_GOOGLE_PAY_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_IMMEDIATE_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_IMMEDIATE_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_LATER_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_LATER_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_OVER_TIME_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_OVER_TIME_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_TWINT_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_TWINT_ID]['descriptions']['en']['desc'],
    ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_APPLE_PAY_ID =>
        Module::PAYMENT_DEFINTIONS[Module::PAYMENT_APPLE_PAY_ID]['descriptions']['en']['desc']
];

foreach ($paymentLang as $key => $description) {
    $key = 'SHOP_MODULE_' . $key;
    $paymentLang = [
        $key => $description,
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manual',
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Days',
        $key . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Immediate',
        'HELP_' . $key => 'In Adyen you can define the delay of the capture for'
            . $description . ': "Immediate", "after n days" or "Manual".'
            . ' The Adyen setting must correspond to that of the shop. In the "Manual" case'
            . ', under Manage Orders > Orders > Tab Adyen, the capture can be initiated.'
    ];
    $aLang += $paymentLang;
}

