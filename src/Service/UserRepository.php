<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Service;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Core\Config as EshopCoreConfig;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\State;

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

    /** @var EshopRegistry */
    private $registry;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ContextInterface $context,
        EshopCoreConfig $config,
        EshopRegistry $registry
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
        $this->config = $config;
        $this->registry = $registry;
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

    public function getUserCountryIso(): string
    {
        $user = $this->registry->getSession()->getUser();
        $country = oxNew(Country::class);
        $country->load($user->getFieldData('oxcountryid'));
        return (string) $country->getFieldData('oxisoalpha2');
    }

    public function getUserStateIso(): string
    {
        $user = $this->registry->getSession()->getUser();
        $country = oxNew(State::class);
        $country->load($user->getFieldData('oxstateid'));
        return (string) $country->getFieldData('oxisoalpha2');
    }
}
