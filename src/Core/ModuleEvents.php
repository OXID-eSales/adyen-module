<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

use Exception;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;
use OxidSolutionCatalysts\Adyen\Model\Payment;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\StaticContents;
use OxidEsales\Eshop\Application\Model\Payment as EshopModelPayment;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class defines what module does on Shop events.
 *
 * @codeCoverageIgnore
 */
final class ModuleEvents
{
    /**
     * Execute action on activate event
     *
     * @throws Exception
     */
    public static function onActivate(): void
    {
        // execute module migrations
        self::executeModuleMigrations();

        // add static contents like payment methods
        self::addStaticContents();
    }

    /**
     * Execute action on deactivate event
     *
     * @throws Exception
     */
    public static function onDeactivate(): void
    {
        $activePaymentMethods = [];
        $paymentIds = array_keys(Module::PAYMENT_DEFINTIONS);
        foreach ($paymentIds as $paymentId) {
            /** @var Payment $paymentMethod */
            $paymentMethod = oxNew(EshopModelPayment::class);
            if (
                $paymentMethod->load($paymentId)
                && $paymentMethod->getAdyenBoolData('oxactive') === true
            ) {
                $paymentMethod->assign([
                    'oxpayments__oxactive' => false
                ]);
                $paymentMethod->save();
                $activePaymentMethods[] = $paymentId;
            }
        }

        /** @var ModuleSettings $service */
        $service = self::getModuleSettingsService();
        $service->saveActivePayments($activePaymentMethods);
    }

    /**
     * Execute necessary module migrations on activate event
     */
    private static function executeModuleMigrations(): void
    {
        $migrations = (new MigrationsBuilder())->build();

        $output = new BufferedOutput();
        $migrations->setOutput($output);
        $neeedsUpdate = $migrations->execute('migrations:up-to-date', 'osc_adyen');

        if ($neeedsUpdate) {
            $migrations->execute('migrations:migrate', 'osc_adyen');
        }
    }

    /**
     * add Static Contents like payment methods
     *
     * @return void
     */
    private static function addStaticContents(): void
    {
        /** @var StaticContents $service */
        $service = self::getStaticContentService();
        $service->ensurePaymentMethods();
    }

    private static function getStaticContentService(): StaticContents
    {
        /*
        Normally I would fetch the StaticContents service like this:

        $service = ContainerFactory::getInstance()
            ->getContainer()
            ->get(StaticContents::class);

        But the services are not ready when the onActivate method is triggered.
        That's why I build the containers by hand as an exception.:
        */

        /** @var ContainerInterface $container */
        $container = ContainerFactory::getInstance()
            ->getContainer();
        /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);

        $moduleSettings = self::getModuleSettingsService();

        return new StaticContents(
            $queryBuilderFactory,
            $moduleSettings
        );
    }

    private static function getModuleSettingsService(): ModuleSettings
    {
        /*
        Normally I would fetch the ModuleSettings service like this:

        $service = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleSettings::class);

        But the services are not ready when the onActivate method is triggered.
        That's why I build the containers by hand as an exception.:
        */

        /** @var ContainerInterface $container */
        $container = ContainerFactory::getInstance()
            ->getContainer();
        /** @var ModuleSettingBridgeInterface $moduleSettingBridge */
        $moduleSettingBridge = $container->get(ModuleSettingBridgeInterface::class);

        return new ModuleSettings(
            $moduleSettingBridge
        );
    }
}
