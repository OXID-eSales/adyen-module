<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Core;

final class Module
{
    public const MODULE_ID = 'osc_adyen';

    public const OETM_GREETING_TEMPLATE_VARNAME = 'oetm_greeting';

    public const OETM_COUNTER_TEMPLATE_VARNAME = 'oetm_greeting_counter';

    public const DEFAULT_PERSONAL_GREETING_LANGUAGE_CONST = 'OEMODULETEMPLATE_GREETING_GENERIC';
}
