<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Service;

use OxidSolutionCatalysts\Adyen\Core\Module;
use OxidSolutionCatalysts\Adyen\Service\TranslationMapper;
use PHPUnit\Framework\TestCase;

class TranslationMapperTest extends TestCase
{
    /**
     * @covers \OxidSolutionCatalysts\Adyen\Service\TranslationMapper::mapReturnResultCode
     * @dataProvider getTestData
     */
    public function testMapReturnResultCode($resultCode, $translationIdExpected)
    {
        $mapper = new TranslationMapper();
        $translationIdActual = $mapper->mapReturnResultCode($resultCode);
        $this->assertEquals($translationIdExpected, $translationIdActual);
    }

    public function getTestData(): array
    {
        return [
            [Module::ADYEN_RETURN_RESULT_CODE_CANCELLED, TranslationMapper::OSC_ADYEN_RETURN_REASON_CANCELLED],
            [Module::ADYEN_RETURN_RESULT_CODE_REFUSED, TranslationMapper::OSC_ADYEN_RETURN_REASON_REFUSED],
            ['anything', TranslationMapper::OSC_ADYEN_RETURN_REASON_ERROR],
        ];
    }
}
