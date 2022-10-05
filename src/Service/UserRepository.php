<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Config as EshopCoreConfig;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Eshop\Core\Session as EshopSession;
use OxidEsales\Eshop\Application\Model\Country;

/**
 * @extendable-class
 */
class UserRepository
{
    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ContextInterface */
    private $context;

    /** @var EshopCoreConfig */
    private $config;

    /** @var EshopSession */
    private $session;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ContextInterface $context,
        EshopCoreConfig $config,
        EshopSession $session
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Check if a user with password can be found by provided email for the current shop (or mall)
     */
    public function userAccountExists(string $userEmail): bool
    {
        $userId = $this->getUserId($userEmail, true);

        return !empty($userId);
    }

    /**
     * Check if a user with password can be found by provided email for the current shop (or mall)
     */
    public function guestAccountExists(string $userEmail): bool
    {
        $userId = $this->getUserId($userEmail, false);

        return !empty($userId);
    }

    public function getUserCountryIso(): string
    {
        $country = oxNew(Country::class);
        $country->load($this->getCountryId());
        return (string) $country->getFieldData('oxisoalpha2');
    }

    /**
     * Tries to fetch user country ID
     *
     * @return string
     */
    private function getCountryId(): string
    {
        $countryId = $this->config->getGlobalParameter('delcountryid');

        if (!$countryId) {
            $addressId = $this->session->getVariable('deladrid');
            $deliveryAddress = oxNew(Address::class);
            $countryId = $deliveryAddress->load($addressId) ? $deliveryAddress->getFieldData('oxcountryid') : '';
        }

        if (!$countryId) {
            $user = $this->session->getUser();
            $countryId = $user->isLoaded() ? $user->getFieldData('oxcountryid') : '';
        }

        if (!$countryId) {
            $homeCountry = $this->config->getConfigParam('aHomeCountry');
            $countryId = is_array($homeCountry) ? current($homeCountry) : '';
        }

        return $countryId;
    }

    private function getUserId(string $userEmail, bool $hasPassword): string
    {
        $userId = '';

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->queryBuilderFactory->create();

        $parameters = [
            'oxusername' => $userEmail
        ];

        $passWordCheck = $hasPassword ? 'LENGTH(`oxpassword`) > 0' : 'LENGTH(`oxpassword`) = 0';

        $queryBuilder->select('oxid')
            ->from('oxuser')
            ->where('oxusername = :oxusername')
            ->andWhere($passWordCheck);

        if (!$this->config->getConfigParam('blMallUsers')) {
            $queryBuilder->andWhere('oxshopid = :oxshopid');
            $parameters['oxshopid'] = $this->context->getCurrentShopId();
        }

        /** @var Result $resultDB */
        $resultDB = $queryBuilder->setParameters($parameters)
            ->setMaxResults(1)
            ->execute();

        if (is_a($resultDB, Result::class)) {
            $userId = $resultDB->fetchOne();
        }

        return (string) $userId;
    }
}
