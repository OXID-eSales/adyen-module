<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidSolutionCatalysts\Adyen\Service\Context;
use OxidSolutionCatalysts\Adyen\Service\SessionSettings;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

abstract class AbstractSessionSettingsTest extends TestCase
{
    use ServiceContainer;

    /** make sure we use the same session object in object under test: SessionSettings and in test code */
    private ?Session $session = null;

    private ?SessionSettings $sessionSettings = null;

    protected function createSessionSettings(): SessionSettings
    {
        if (is_null($this->sessionSettings)) {
            $this->sessionSettings = new SessionSettings(
                $this->getSession(),
                $this->getServiceFromContainer(Context::class)
            );
        }
        return $this->sessionSettings;
    }
    protected function setValueInSession(string $key, $value = null): void
    {
        $this->getSession()->setVariable($key, $value);
    }

    protected function getValueFromSession(string $key)
    {
        return $this->getSession()->getVariable($key);
    }

    protected function deleteInSession(string $key): void
    {
        $this->getSession()->deleteVariable($key);
    }

    protected function getSession(): Session
    {
        if (is_null($this->session)) {
            $this->session = Registry::getSession();
        }

        return $this->session;
    }
}
