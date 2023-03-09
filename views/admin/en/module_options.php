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
$keyDelayKlarna = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_KLARNA_ID;
$descKlarna = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_KLARNA_ID]['descriptions']['de']['desc'];
$keyDelayTwint = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_TWINT_ID;
$descTwint = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_TWINT_ID]['descriptions']['de']['desc'];

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
    'SHOP_MODULE_osc_adyen_OperationMode_sandbox' => 'Sandbox',
    'SHOP_MODULE_osc_adyen_OperationMode_live' => 'Live',
    'SHOP_MODULE_' . ModuleSettings::LOGGING_ACTIVE => 'Logging active?',
    'SHOP_MODULE_' . ModuleSettings::ANALYTICS_ACTIVE => 'Analytics active?',

    'SHOP_MODULE_osc_adyen_SandboxAPIKey' => 'API Key',
    'SHOP_MODULE_osc_adyen_SandboxClientKey' => 'Client Key',
    'SHOP_MODULE_osc_adyen_SandboxHmacSignature' => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_osc_adyen_SandboxMerchantAccount' => 'Merchant Account',
    'SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'PayPal Merchant Id',
    'SHOP_MODULE_osc_adyen_SandboxGooglePayMerchantId' => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_SandboxGooglePayMerchantId' => 'The Live-"Google Pay Merchant Id" is only required if you want to use Google Pay via Adyen',
    'HELP_SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'The Live-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    'SHOP_MODULE_osc_adyen_KlarnaPaymentType' => 'Payment Type',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna' => ' Klarna — Pay later',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna_paynow' => ' Klarna — Pay Now',
    'SHOP_MODULE_osc_adyen_KlarnaPaymentType_klarna_account' => ' Klarna — Pay Over Time',

    'SHOP_MODULE_osc_adyen_LiveAPIKey' => 'API Key',
    'SHOP_MODULE_osc_adyen_LiveClientKey' => 'Client Key',
    'SHOP_MODULE_osc_adyen_LiveHmacSignature' => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_osc_adyen_LiveMerchantAccount' => 'Merchant Account',
    'SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'PayPal Merchant Id',
    'SHOP_MODULE_osc_adyen_LiveGooglePayMerchantId' => 'Google Pay Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'The Sandbox-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',
    'SHOP_MODULE_osc_adyen_LiveEndpointPrefix' => 'Live Endpoint Prefix',
    'HELP_SHOP_MODULE_osc_adyen_LiveEndpointPrefix' => 'In the live system, a prefix is needed for each merchant. The prefix is defined in Adyen. (check: https://docs.adyen.com/development-resources/live-endpoints)',
    'SHOP_MODULE_osc_adyen_Languages' => 'Language localizations matching the OXID language',
    'HELP_SHOP_MODULE_osc_adyen_Languages' =>
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
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayKlarna, $descKlarna),
);
$aLang = array_merge(
    $aLang,
    $moduleOptionsCaptureDelay->getTranslationENArrayForOption($keyDelayTwint, $descTwint),
);