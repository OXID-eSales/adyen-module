<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidSolutionCatalysts\Adyen\Controller\AdyenJSController;
use OxidSolutionCatalysts\Adyen\Controller\AdyenWebhookController;
use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderArticle;
use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderList;
use OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController;
use OxidSolutionCatalysts\Adyen\Controller\OrderController;
use OxidSolutionCatalysts\Adyen\Controller\PaymentController;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\Address;
use OxidSolutionCatalysts\Adyen\Model\Country;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Model\PaymentGateway;
use OxidSolutionCatalysts\Adyen\Model\User;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => Module::MODULE_ID,
    'title' => [
        'de' => Module::MODULE_NAME_DE,
        'en' => Module::MODULE_NAME_EN
    ],
    'description' => [
        'de' => 'Nutzung der Online-Bezahldienste von Adyen.',
        'en' => 'Use of the online payment services from Adyen.'
    ],
    'thumbnail' => 'out/pictures/logo.png',
    'version' => Module::MODULE_VERSION_FULL,
    'author' => 'OXID eSales AG',
    'url' => 'https://www.oxid-esales.com',
    'email' => 'support@oxid-esales.com',
    'extend' => [
        // model
        \OxidEsales\Eshop\Application\Model\Address::class => Address::class,
        \OxidEsales\Eshop\Application\Model\Country::class => Country::class,
        \OxidEsales\Eshop\Application\Model\Order::class => Order::class,
        \OxidEsales\Eshop\Application\Model\Payment::class => Payment::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class => PaymentGateway::class,
        \OxidEsales\Eshop\Application\Model\User::class => User::class,
        // core
        \OxidEsales\Eshop\Core\ViewConfig::class => ViewConfig::class,
        // admin-controller
        \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class => OrderList::class,
        \OxidEsales\Eshop\Application\Controller\Admin\OrderArticle::class => OrderArticle::class,
        // frontend-controller
        \OxidEsales\Eshop\Application\Controller\OrderController::class => OrderController::class,
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => PaymentController::class,
    ],
    'events' => [
        'onActivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onDeactivate'
    ],
    'controllers' => [
        // admin
        'adyen_admin_order' => AdminOrderController::class,
        // frontend
        'AdyenJSController' => AdyenJSController::class,
        'AdyenWebhookController' => AdyenWebhookController::class
    ],
    'templates' => [
        // admin
        'osc_adyen_order.tpl' => 'osc/adyen/views/admin/tpl/osc_adyen_order.tpl',
        // frontend - paymentpage
        'modules/osc/adyen/payment/adyen_assets.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_assets.tpl',
        'modules/osc/adyen/payment/adyen_payment.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_payment.tpl',
        'modules/osc/adyen/payment/adyen_payment_inauthorisation.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_payment_inauthorisation.tpl',
        'modules/osc/adyen/payment/adyen_payment_psp.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_payment_psp.tpl',
        // frontend - orderpage
        'modules/osc/adyen/payment/adyen_order_submit.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_order_submit.tpl',
        // frontend - account
        'modules/osc/adyen/account/order_adyen.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen.tpl',
        // frontend - mails
        'modules/osc/adyen/email/order_adyen_html.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen_html.tpl',
        'modules/osc/adyen/email/order_adyen_plain.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen_plain.tpl',
        // adyen js api
        'modules/osc/adyen/payment/adyen_assets_configuration.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_assets_configuration.tpl',
    ],
    'blocks' => [
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => 'views/frontend/blocks/page/checkout/select_payment.tpl'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'checkout_payment_main',
            'file' => 'views/frontend/blocks/page/checkout/checkout_payment_main.tpl'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'checkout_payment_nextstep',
            'file' => 'views/frontend/blocks/page/checkout/checkout_payment_nextstep.tpl'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'checkout_payment_errors',
            'file' => 'views/frontend/blocks/page/checkout/checkout_payment_errors.tpl'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_btn_submit_bottom',
            'file' => 'views/frontend/blocks/page/checkout/checkout_order_btn_submit_bottom.tpl'
        ],
        [
            'template' => 'page/account/order.tpl',
            'block' => 'account_order_history_cart_items',
            'file' => 'views/frontend/blocks/page/account/account_order_history_cart_items.tpl'
        ],
        [
            'template' => 'email/html/order_cust.tpl',
            'block' => 'email_html_order_cust_orderemail',
            'file' => 'views/frontend/blocks/email/html/email_html_order_cust_orderemail.tpl'
        ],
        [
            'template' => 'email/html/order_owner.tpl',
            'block' => 'email_html_order_owner_orderemail',
            'file' => 'views/frontend/blocks/email/html/email_html_order_owner_orderemail.tpl'
        ],
        [
            'template' => 'email/html/ordershipped.tpl',
            'block' => 'email_html_ordershipped_oxordernr',
            'file' => 'views/frontend/blocks/email/html/email_html_ordershipped_oxordernr.tpl'
        ],
        [
            'template' => 'email/plain/order_cust.tpl',
            'block' => 'email_plain_order_cust_orderemail',
            'file' => 'views/frontend/blocks/email/plain/email_plain_order_cust_orderemail.tpl'
        ],
        [
            'template' => 'email/plain/order_owner.tpl',
            'block' => 'email_plain_order_owner_orderemail',
            'file' => 'views/frontend/blocks/email/plain/email_plain_order_owner_orderemail.tpl'
        ],
        [
            'template' => 'email/plain/ordershipped.tpl',
            'block' => 'email_plain_ordershipped_oxordernr',
            'file' => 'views/frontend/blocks/email/plain/email_plain_ordershipped_oxordernr.tpl'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_address',
            'file' => 'views/frontend/blocks/page/checkout/order_checkout_order_address.tpl'
        ],
        //admin
        [
            'template' => 'module_config.tpl',
            'block' => 'admin_module_config_form',
            'file' => 'views/admin/blocks/admin_module_config_form.tpl'
        ],
    ],
    'settings' => [
        [
            'group'       => 'osc_adyen_API',
            'name'        => ModuleSettings::OPERATION_MODE,
            'type'        => 'select',
            'constraints' => 'sandbox|live',
            'value'       => 'sandbox'
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => ModuleSettings::LOGGING_ACTIVE,
            'type' => 'bool',
            'value' => false
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => ModuleSettings::ANALYTICS_ACTIVE,
            'type' => 'bool',
            'value' => false
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_API_KEY,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_CLIENT_KEY,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_HMAC_SIGNATURE,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_MERCHANT_ACCOUNT,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_PAYPAL_MERCHANT_ID,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => ModuleSettings::SANDBOX_GOOGLE_PAY_MERCHANT_ID,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_API_KEY,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_CLIENT_KEY,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_ENDPOINT_PREFIX,
            'type' => 'str',
            'value' => '[yourprefix]'
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_HMAC_SIGNATURE,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_MERCHANT_ACCOUNT,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_PAYPAL_MERCHANT_ID,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => ModuleSettings::LIVE_GOOGLE_PAY_MERCHANT_ID,
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_CREDITCARD_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_PAYPAL_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_GOOGLE_PAY_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_IMMEDIATE_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_LATER_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_KLARNA_OVER_TIME_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_TWINT_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => ModuleSettings::CAPTURE_DELAY . Module::PAYMENT_APPLE_PAY_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_Languages',
            'name' => ModuleSettings::LANGUAGES,
            'type' => 'aarr',
            'value' => [
                'de' => 'de_DE',
                'en' => 'en_US',
            ]
        ],
        [
            'group' => null,
            'name' => ModuleSettings::ACTIVE_PAYMENTS,
            'type' => 'arr',
            'value' => []
        ],
    ],
];
