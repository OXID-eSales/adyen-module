<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\Adyen\Tests\Integration\Service;

use OxidEsales\Eshop\Application\Model\User;
use OxidSolutionCatalysts\Adyen\Service\UserRepository;
use OxidSolutionCatalysts\Adyen\Traits\ServiceContainer;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase
{
    use ServiceContainer;

    public function setup(): void
    {
        parent::setUp();
        foreach ($this->providerTestUserData() as $dataSet) {
            [$userId, $userName, $userPassword] = $dataSet;
            $user = oxNew(User::class);
            $user->setId($userId);
            $user->assign([
                'oxuser__oxactive' => 1,
                'oxuser__oxusername' => $userName
            ]);
            $user->setPassword($userPassword);
            $user->save();
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->providerTestUserData() as $dataSet) {
            [$userId, , ] = $dataSet;
            $user = oxNew(User::class);
            $user->load($userId);
            $user->delete();
        }
    }

    /**
     * @dataProvider providerTestUserData
     */
    public function testUserAccountExists(
        string $userId,
        string $userName,
        string $userPassword
    ): void {


        $service = $this->getServiceFromContainer(UserRepository::class);
        $this->assertSame((bool)$userPassword, $service->userAccountExists($userName));
    }

    /**
     * @dataProvider providerTestUserData
     */
    public function testGuestAccountExists(
        string $userId,
        string $userName,
        string $userPassword
    ): void {
        $service = $this->getServiceFromContainer(UserRepository::class);
        $this->assertSame(!$userPassword, $service->guestAccountExists($userName));
    }

    public function providerTestUserData(): array
    {
        return [
            [
                '123',
                'accountuser@dummy.dev',
                'accountuser'
            ],
            [
                '456',
                'guestuser@dummy.dev',
                ''
            ]
        ];
    }
}
