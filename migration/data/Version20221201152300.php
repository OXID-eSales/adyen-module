<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Facts\Facts;
use OxidSolutionCatalysts\Adyen\Core\Module;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201152300 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->createAdyenPSPReference($schema);
    }

    public function down(Schema $schema): void
    {
    }

    /**
     * create Adyen PSP Reference in oxorder
     */
    protected function createAdyenPSPReference(Schema $schema): void
    {
        $order = $schema->getTable('oxorder');

        if (!$order->hasColumn('ADYENORDERREFERENCE')) {
            $order->addColumn(
                'ADYENORDERREFERENCE',
                Types::STRING,
                ['columnDefinition' => 'char(32) collate latin1_general_ci']
            );
        }
    }
}
