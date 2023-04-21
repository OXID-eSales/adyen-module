<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Codeception\Acceptance\Page;

use OxidSolutionCatalysts\Adyen\Tests\Codeception\AcceptanceTester;
use Codeception\Actor;
use OxidEsales\Codeception\Page\Page as CorePage;

class Page extends CorePage
{
    protected AcceptanceTester $I;

    public function __construct(Actor $I)
    {
        parent::__construct($I);

        /**
         * got some trouble with code completion for $this->user->waitForElement,
         * so we assign here $I of Type AcceptanceTester
         */
        if ($I instanceof AcceptanceTester) {
            $this->I = $I;
        }
    }
}
