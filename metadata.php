<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'          => 'osc_adyen',
    'title'       => 'CHANGE MY TITLE',
    'description' =>  '',
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '1.0.0-rc.1',
    'author'      => 'OXID eSales AG',
    'url'         => '',
    'email'       => '',
    'extend'      => [
        \OxidEsales\Eshop\Application\Model\User::class => \OxidSolutionCatalysts\Adyen\Model\User::class,
        \OxidEsales\Eshop\Application\Controller\StartController::class => \OxidSolutionCatalysts\Adyen\Controller\StartController::class
    ],
    'controllers' => [
        'oetmgreeting' => \OxidSolutionCatalysts\Adyen\Controller\GreetingController::class
    ],
    'templates'   => [
        'greetingtemplate.tpl' => 'osc/adyen/views/templates/greetingtemplate.tpl',
    ],
    'events' => [
        'onActivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onDeactivate'
    ],
    'blocks'      => [
        [
            //It is possible to replace blocks by theme, to do so add 'theme' => '<theme_name>' key/value in here
            'template' => 'page/shop/start.tpl',
            'block' => 'start_welcome_text',
            'file' => 'views/blocks/oetm_start_welcome_text.tpl'
        ]
    ],
    'settings' => [
        /** Main */
        [
            'group'       => 'oemoduletemplate_main',
            'name'        => 'oemoduletemplate_GreetingMode',
            'type'        => 'select',
            'constraints' => 'generic|personal',
            'value'       => 'generic'
        ],
        [
            'group' => 'oemoduletemplate_main',
            'name'  => 'oemoduletemplate_BrandName',
            'type'  => 'str',
            'value' => 'Testshop'
        ],
        [
            'group' => 'oemoduletemplate_main',
            'name' => 'oemoduletemplate_LoggerEnabled',
            'type' => 'bool',
            'value' => false
        ],
        [
            'group' => 'oemoduletemplate_main',
            'name' => 'oemoduletemplate_Timeout',
            'type' => 'num',
            'value' => 30
            //'value' => 30.5
        ],
        [
            'group' => 'oemoduletemplate_main',
            'name' => 'oemoduletemplate_AlwaysOpenCats',
            'type' => 'arr',
            'value' => ['Price', 'Manufacturer']
        ],
        [
            'group' => 'oemoduletemplate_main',
            'name' => 'oemoduletemplate_Channels',
            'type' => 'aarr',
            'value' => ['1' => 'de', '2' => 'en']
        ],
        [
            'group'       => 'oemoduletemplate_main',
            'name' => 'oemoduletemplate_Password',
            'type' => 'password',
            'value' => 'changeMe',
            'position' => 3
        ]
    ],
];
