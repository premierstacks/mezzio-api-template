<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Database\Migrations;
use App\Database\Migrator;
use App\Database\PdoConfig;
use App\Database\PdoConfigInterface;
use App\Handler\PingHandler;
use App\Migration\CreateUsersTableMigration;
use App\Provider\PdoProvider;
use PDO;

final readonly class ConfigProvider
{
    /**
     * @return array<int|string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                CreateUsersTableMigration::class => CreateUsersTableMigration::class . '::provide',
                Migrations::class => Migrations::class . '::provide',
                Migrator::class => Migrator::class . '::provide',
                PingHandler::class => PingHandler::class . '::provide',
                PdoConfigInterface::class => PdoConfig::class . '::provide',
                PDO::class => PdoProvider::class . '::provide',
            ],
        ];
    }
}
