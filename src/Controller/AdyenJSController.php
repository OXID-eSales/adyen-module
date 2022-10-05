<?php

namespace OxidSolutionCatalysts\Adyen\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidSolutionCatalysts\Adyen\Traits\AdyenAPI;

class AdyenJSController extends FrontendController
{
    use AdyenAPI;
}
