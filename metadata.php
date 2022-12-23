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
use OxidSolutionCatalysts\Adyen\Model\Basket;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Model\PaymentGateway;

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
        \OxidEsales\Eshop\Application\Model\Order::class => Order::class,
        \OxidEsales\Eshop\Application\Model\Payment::class => Payment::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class => PaymentGateway::class,
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
        'modules/osc/adyen/payment/adyen_payment_nextstep.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_payment_nextstep.tpl',
        // frontend - orderpage
        'modules/osc/adyen/payment/adyen_order_submit.tpl' => 'osc/adyen/views/frontend/tpl/payment/adyen_order_submit.tpl',
        // frontend - account
        'modules/osc/adyen/account/order_adyen.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen.tpl',
        // frontend - mails
        'modules/osc/adyen/email/order_adyen_html.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen_html.tpl',
        'modules/osc/adyen/email/order_adyen_plain.tpl' => 'osc/adyen/views/frontend/tpl/account/order_adyen_plain.tpl',
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
            'name'        => 'osc_adyen_OperationMode',
            'type'        => 'select',
            'constraints' => 'sandbox|live',
            'value'       => 'sandbox'
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_LoggingActive',
            'type' => 'bool',
            'value' => false
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => 'osc_adyen_SandboxAPIKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => 'osc_adyen_SandboxClientKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => 'osc_adyen_SandboxHmacSignature',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => 'osc_adyen_SandboxMerchantAccount',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_SANDBOX',
            'name' => 'osc_adyen_SandboxPayPalMerchantId',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LiveAPIKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LiveClientKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LiveHmacSignature',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LiveMerchantAccount',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LivePayPalMerchantId',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => 'osc_adyen_CaptureDelay_' . Module::PAYMENT_CREDITCARD_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_CaptureDelay',
            'name' => 'osc_adyen_CaptureDelay_' . Module::PAYMENT_PAYPAL_ID,
            'type' => 'select',
            'value' => Module::ADYEN_CAPTURE_DELAY_MANUAL,
            'constraints' => Module::ADYEN_CAPTURE_DELAY_MANUAL . '|' .
                Module::ADYEN_CAPTURE_DELAY_DAYS . '|' .
                Module::ADYEN_CAPTURE_DELAY_IMMEDIATE
        ],
        [
            'group' => 'osc_adyen_Languages',
            'name' => 'osc_adyen_Languages',
            'type' => 'aarr',
            'value' => [
                'de' => 'de_DE',
                'en' => 'en_US',
            ]
        ],
        [
            'group' => null,
            'name' => 'osc_adyen_activePayments',
            'type' => 'arr',
            'value' => []
        ],
    ],
];
