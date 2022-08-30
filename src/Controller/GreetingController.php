<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidSolutionCatalysts\Adyen\Core\Module as ModuleCore;
use OxidSolutionCatalysts\Adyen\Model\GreetingTracker;
use OxidSolutionCatalysts\Adyen\Model\User as TemplateModelUser;
use OxidSolutionCatalysts\Adyen\Service\GreetingMessage;
use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Service\Repository;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;

/**
 * @extendable-class
 *
 * This is a brand new (module own) controller which extends from the
 * shop frontend controller class.
 */
class GreetingController extends FrontendController
{
    use ServiceContainer;

    /**
     * Current view template
     *
     * @var string
     */
    protected $_sThisTemplate = 'greetingtemplate.tpl';

    /**
     * Rendering method.
     *
     * @return mixed
     */
    public function render()
    {
        $template       = parent::render();
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);
        $repository     = $this->getServiceFromContainer(Repository::class);

        //This way information is transported to the template layer, add to _aViewData array and
        //use as [{$oetm_greeting}] in the (smarty) template.
        //NOTE: setting default values prevents smarty from running into warnings from missing data
        $this->_aViewData[ModuleCore::OETM_GREETING_TEMPLATE_VARNAME] = '';
        $this->_aViewData[ModuleCore::OETM_COUNTER_TEMPLATE_VARNAME]  = 0;

        /** @var TemplateModelUser $user */
        $user = $this->getUser();

        if (is_a($user, EshopModelUser::class) && $moduleSettings->isPersonalGreetingMode()) {
            $this->_aViewData[ModuleCore::OETM_GREETING_TEMPLATE_VARNAME] = $user->getPersonalGreeting();
            /** @var GreetingTracker $tracker */
            $tracker = $repository->getTrackerByUserId($user->getId());
            $this->_aViewData[ModuleCore::OETM_COUNTER_TEMPLATE_VARNAME] = $tracker->getCount();
        }

        return $template;
    }

    /**
     * NOTE: every public method in the controller will become part of the public API.
     *       A controller public method can be called via browser by cl=<controllerkey>&fnc=<methodname>.
     *       Take care not to accidentally expose methods that should not be part of the API.
     *       Leave the business logic to the service layer.
     */
    public function updateGreeting(): void
    {
        $moduleSettings = $this->getServiceFromContainer(ModuleSettings::class);

        /** @var EshopModelUser $user */
        $user = $this->getUser();

        if (!is_object($user) || !$moduleSettings->isPersonalGreetingMode()) {
            return;
        }

        $greetingService = $this->getServiceFromContainer(GreetingMessage::class);
        $greetingService->saveGreeting($user);
    }
}
