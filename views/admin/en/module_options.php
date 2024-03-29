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
$descCreditCard = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['en']['desc'];
$keyDelayPayPal = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_PAYPAL_ID;
$descPayPal = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['en']['desc'];
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
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics active?',

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
$moduleOptionsCaptureDelay = new ModuleOptionsCaptureDelay();

$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayCreditCard, $descCreditCard),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayPayPal, $descPayPal),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayGooglePay, $descGooglePay),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayKlarnaImmediate, $descKlarnaImmediate),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayKlarnaLater, $descKlarnaLater),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayKlarnaOverTime, $descKlarnaOverTime),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayTwint, $descTwint),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayApplePay, $descApplePay),
);
