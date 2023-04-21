<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidSolutionCatalysts\Adyen\Model\ModuleOptionsCaptureDelay;
use OxidSolutionCatalysts\Adyen\Core\Module;

class ModuleOptionsCaptureDelayTest extends UnitTestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\ModuleOptionsCaptureDelay::getTranslationDEArrayForOption
     */
    public function testGetTranslationDEArrayForOption(): void
    {
        $keyDelay = 'keyDelay';
        $description = 'description';

        $model = new ModuleOptionsCaptureDelay();
        $actual = $model->getTranslationDEArrayForOption($keyDelay, $description);

        $this->assertEquals(
            [
                $keyDelay => $description,
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manuell',
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Tagen',
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Sofort',
                'HELP_' . $keyDelay => 'In Adyen kann man die Verzögerung des Geldeinzugs für '
                    . $description . ' definieren: "Immediate", "after n days" oder "Manual".'
                    . 'Die Adyen-Einstellung muss mit der vom Shop korrespondieren.'
                    . ' Im Fall "Manual" kann unter Bestellungen verwalten > Bestellungen > Reiter Adyen,'
                    . ' der Geldeinzug angestoßen werden.',
            ],
            $actual
        );
    }

    /**
     * @covers \OxidSolutionCatalysts\Adyen\Model\ModuleOptionsCaptureDelay::getTranslationENArrayForOption
     */
    public function testGetTranslationENArrayForOption(): void
    {
        $keyDelay = 'keyDelay';
        $description = 'description';

        $model = new ModuleOptionsCaptureDelay();
        $actual = $model->getTranslationENArrayForOption($keyDelay, $description);

        $this->assertEquals(
            [
                $keyDelay => $description,
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manual',
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Days',
                $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Immediate',
                'HELP_' . $keyDelay =>
                    'In Adyen you can define the delay of the capture for ' . $description
                    . ': "Immediate", "after n days" or "Manual".'
                    . ' The Adyen setting must correspond to that of the shop. In the "Manual" case'
                    . ', under Manage Orders > Orders > Tab Adyen, the capture can be initiated.',
            ],
            $actual
        );
    }
}
