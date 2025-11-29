<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Factory\AdapterInterfaceFactory;
use App\Handler\PingHandler;
use App\Migration\CreateMigrationsTable;
use App\Migration\CreateUsersTableMigration;
use App\Migrator\Migrations;
use App\Migrator\Migrator;
use App\Migrator\MigratorExecutor;
use App\Migrator\MigratorMarker;
use App\RowGateway\UsersRowGateway;
use App\TableGateway\MigrationsTableGateway;
use App\TableGateway\UsersTableGateway;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;

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
                Adapter::class => AdapterInterfaceFactory::class . '::factory',
                AdapterInterface::class => AdapterInterfaceFactory::class . '::factory',
                CreateMigrationsTable::class => CreateMigrationsTable::class . '::factory',
                CreateUsersTableMigration::class => CreateUsersTableMigration::class . '::factory',
                Migrations::class => Migrations::class . '::factory',
                MigrationsTableGateway::class => MigrationsTableGateway::class . '::factory',
                Migrator::class => Migrator::class . '::factory',
                MigratorExecutor::class => MigratorExecutor::class . '::factory',
                MigratorMarker::class => MigratorMarker::class . '::factory',
                PingHandler::class => PingHandler::class . '::factory',
                UsersRowGateway::class => UsersRowGateway::class . '::factory',
                UsersTableGateway::class => UsersTableGateway::class . '::factory',
            ],
        ];
    }
}
