<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidSolutionCatalysts\Adyen\Controller\Admin\AdminOrderController;
use OxidSolutionCatalysts\Adyen\Core\ViewConfig;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Model\Order;
use OxidSolutionCatalysts\Adyen\Controller\Admin\OrderList;

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'osc_adyen',
    'title' => [
        'de' => 'Adyen Payment für OXID',
        'en' => 'Adyen Payment for OXID'
    ],
    'description' => [
        'de' => 'Nutzung der Online-Bezahldienste von Adyen.',
        'en' => 'Use of the online payment services from Adyen.'
    ],
    'thumbnail' => 'out/pictures/logo.png',
    'version' => '1.0.0-rc.1',
    'author' => 'OXID eSales AG',
    'url' => 'https://www.oxid-esales.com',
    'email' => 'support@oxid-esales.com',
    'extend' => [
        // model
        \OxidEsales\Eshop\Application\Model\Payment::class => Payment::class,
        \OxidEsales\Eshop\Application\Model\Order::class => Order::class,
        // core
        \OxidEsales\Eshop\Core\ViewConfig::class => ViewConfig::class,
        // admin-controller
        \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class => OrderList::class,
    ],
    'events' => [
        'onActivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onDeactivate'
    ],
    'controllers' => [
        'adyen_admin_order' => AdminOrderController::class,
    ],
    'templates' => [
        // admin
        'osc_adyen_order.tpl' => 'osc/adyen/views/admin/tpl/osc_adyen_order.tpl',
        // frontend - paymentpage
        'modules/osc/adyen/payment/payment_adyen.tpl' => 'osc/adyen/views/frontend/tpl/payment/payment_adyen.tpl',
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
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxAPIKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxClientKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxHmacSignature',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxMerchantAccount',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxNotificationUsername',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_SandboxNotificationPassword',
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
            'name' => 'osc_adyen_LiveNotificationUsername',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_LIVE',
            'name' => 'osc_adyen_LiveNotificationPassword',
            'type' => 'str',
            'value' => ''
        ],
    ],
];
