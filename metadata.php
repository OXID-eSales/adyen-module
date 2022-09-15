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
    'title' => [
        'de' => 'Adyen Payment für OXID',
        'en' => 'Adyen Payment for OXID'
    ],
    'description' => [
        'de' => 'Nutzung der Online-Bezahldienste von Adyen.',
        'en' => 'Use of the online payment services from Adyen.'
    ],
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '1.0.0-rc.1',
    'author'      => 'OXID eSales AG',
    'url'         => 'https://www.oxid-esales.com',
    'email'       => 'support@oxid-esales.com',
    'extend'      => [
        \OxidEsales\Eshop\Application\Model\User::class => \OxidSolutionCatalysts\Adyen\Model\User::class,
    ],
    'templates'   => [
    ],
    'events' => [
        'onActivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\OxidSolutionCatalysts\Adyen\Core\ModuleEvents::onDeactivate'
    ],
    'blocks'      => [
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
            'name' => 'osc_adyen_LiveAPIKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_API',
            'name' => 'osc_adyen_LiveClientKey',
            'type' => 'str',
            'value' => ''
        ],
        [
            'group' => 'osc_adyen_Debug',
            'name' => 'osc_adyen_LoggingActive',
            'type' => 'bool',
            'value' => false
        ],
    ],
];
