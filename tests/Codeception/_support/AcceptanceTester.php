<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception;

use OxidEsales\EshopCommunity\Core\FileCache;
use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidEsales\Codeception\Page\Home;
use OxidEsales\Facts\Facts;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
final class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    use \Codeception\Lib\Actor\Shared\Retry;

    use ServiceContainer;

    /**
     * Open shop first page.
     */
    public function openShop(): Home
    {
        $I        = $this;
        $homePage = new Home($I);
        $I->amOnPage($homePage->URL);

        return $homePage;
    }

    public function getDemoUserName(): string
    {
        return 'user@oxid-esales.com';
    }

    public function getDemoUserPassword(): string
    {
        $facts = new Facts();

        return $facts->isEnterprise() ? 'useruser' : 'user';
    }

    public function setModuleActive(bool $active = true): void
    {
        $command = $active ? 'activate' : 'deactivate';

        exec((new Facts())->getShopRootPath() . '/bin/oe-console oe:module:' . $command . ' ' . Module::MODULE_ID);
    }

    public function clearShopCache(): void
    {
        FileCache::clearCache();
    }

    public function getShopUrl(): string
    {
        $facts = new Facts();

        return $facts->getShopUrl();
    }
}
