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
    ],
];
