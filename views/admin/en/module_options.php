<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidSolutionCatalysts\Adyen\Core\Module;

$keyDelayCreditCard = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_CREDITCARD_ID;
$descCreditCard = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_CREDITCARD_ID]['descriptions']['en']['desc'];
$keyDelayPayPal = 'SHOP_MODULE_osc_adyen_CaptureDelay_' . Module::PAYMENT_PAYPAL_ID;
$descPayPal = Module::PAYMENT_DEFINTIONS[Module::PAYMENT_PAYPAL_ID]['descriptions']['en']['desc'];

$aLang = [
    'charset' => 'UTF-8',
    'SHOP_MODULE_GROUP_osc_adyen_API' => 'Adyen API',
    'SHOP_MODULE_GROUP_osc_adyen_SANDBOX' => 'Adyen Access Sandbox',
    'SHOP_MODULE_GROUP_osc_adyen_LIVE' => 'Adyen Access Live',
    'SHOP_MODULE_GROUP_osc_adyen_CaptureDelay' => 'Capture delay for selected Payments',
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
    'SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'PayPal Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_SandboxPayPalMerchantId' => 'The Live-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    'SHOP_MODULE_osc_adyen_LiveAPIKey' => 'API Key',
    'SHOP_MODULE_osc_adyen_LiveClientKey' => 'Client Key',
    'SHOP_MODULE_osc_adyen_LiveHmacSignature' => 'HMAC Code (keyed-hash message authentication code)',
    'SHOP_MODULE_osc_adyen_LiveMerchantAccount' => 'Merchant Account',
    'SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'PayPal Merchant Id',
    'HELP_SHOP_MODULE_osc_adyen_LivePayPalMerchantId' => 'The Sandbox-"PayPal Merchant Id" is only required if you want to use PayPal via Adyen',

    $keyDelayCreditCard => $descCreditCard,
    $keyDelayCreditCard . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manual',
    $keyDelayCreditCard . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Days',
    $keyDelayCreditCard . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Immediate',
    'HELP_' . $keyDelayCreditCard =>
        'In Adyen you can define the delay of the capture for ' . $descCreditCard . ': "Immediate", "after n days" or "Manual".
         The Adyen setting must correspond to that of the shop. In the "Manual" case, under Manage Orders > Orders > Tab Adyen,
         the capture can be initiated.',
    $keyDelayPayPal => $descPayPal,
    $keyDelayPayPal . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manual',
    $keyDelayPayPal . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Days',
    $keyDelayPayPal . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Immediate',
    'HELP_' . $keyDelayPayPal =>
        'In Adyen you can define the delay of the capture for ' . $descPayPal . ': "Immediate", "after n days" or "Manual".
         The Adyen setting must correspond to that of the shop. In the "Manual" case, under Manage Orders > Orders > Tab Adyen,
         the capture can be initiated.',

    'SHOP_MODULE_osc_adyen_Languages' => 'Language localizations matching the OXID language',
    'HELP_SHOP_MODULE_osc_adyen_Languages' =>
        'Enter a suitable language and region localization (ISO 639-1 alpha-2 / ISO 3166-1 alpha-2) for each OXID language
        (Master Settings > Languages > Language > Language abbreviation). One line for each setting (e.g. en => en_US)',
];