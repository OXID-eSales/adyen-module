<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;

class TranslationMapper
{
    public const OSC_ADYEN_RETURN_REASON_CANCELLED = 'OSC_ADYEN_RETURN_REASON_CANCELLED';
    public const OSC_ADYEN_RETURN_REASON_REFUSED = 'OSC_ADYEN_RETURN_REASON_REFUSED';
    public const OSC_ADYEN_RETURN_REASON_ERROR = 'OSC_ADYEN_RETURN_REASON_ERROR';

    public function mapReturnResultCode(string $resultCode): string
    {
        switch ($resultCode) {
            case Module::ADYEN_RETURN_RESULT_CODE_CANCELLED:
                return self::OSC_ADYEN_RETURN_REASON_CANCELLED;
            case Module::ADYEN_RETURN_RESULT_CODE_REFUSED:
                return self::OSC_ADYEN_RETURN_REASON_REFUSED;
            default:
                return self::OSC_ADYEN_RETURN_REASON_ERROR;
        }
    }
}
