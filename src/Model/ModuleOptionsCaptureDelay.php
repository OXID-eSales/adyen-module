<?php

namespace OxidSolutionCatalysts\Adyen\Model;

use OxidSolutionCatalysts\Adyen\Core\Module;

class ModuleOptionsCaptureDelay
{
    public function getTranslationDEArrayForOption(string $keyDelay, string $description)
    {
        return [
            $keyDelay => $description,
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manuell',
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Tagen',
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Sofort',
            'HELP_' . $keyDelay =>
                'In Adyen kann man die Verzögerung des Geldeinzugs für ' . $description . ' definieren: "Immediate", "after n days" oder "Manual".
         Die Adyen-Einstellung muss mit der vom Shop korrespondieren. Im Fall "Manual" kann unter Bestellungen verwalten > Bestellungen > Reiter Adyen,
         der Geldeinzug angestoßen werden.',
        ];
    }
    public function getTranslationENArrayForOption(string $keyDelay, string $description)
    {
        return [
            $keyDelay => $description,
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_MANUAL => 'Manual',
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_DAYS => 'n Days',
            $keyDelay . '_' . Module::ADYEN_CAPTURE_DELAY_IMMEDIATE => 'Immediate',
            'HELP_' . $keyDelay =>
                'In Adyen you can define the delay of the capture for ' . $description . ': "Immediate", "after n days" or "Manual".
         The Adyen setting must correspond to that of the shop. In the "Manual" case, under Manage Orders > Orders > Tab Adyen,
         the capture can be initiated.',
        ];
    }
}