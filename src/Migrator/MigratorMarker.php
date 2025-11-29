<?php

declare(strict_types=1);

namespace App\Migrator;

use App\Migration\CreateMigrationsTable;
use App\TableGateway\MigrationsTableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Psr\Container\ContainerInterface;

use function assert;

final readonly class MigratorMarker
{
    private readonly MigratorExecutor $executor;

    private readonly TableGatewayInterface $gateway;

    public function __construct(MigrationInterface $migration, MigratorExecutor $executor, MigrationsTableGateway $gateway)
    {
        $this->executor = $executor;
        $this->gateway = $gateway;

        foreach ($migration->up() as $ddl) {
            $this->executor->execute($ddl);
        }
    }

    public static function factory(ContainerInterface $container): self
    {
        $migration = $container->get(CreateMigrationsTable::class);
        $executor = $container->get(MigratorExecutor::class);
        $gateway = $container->get(MigrationsTableGateway::class);

        assert($migration instanceof CreateMigrationsTable);
        assert($executor instanceof MigratorExecutor);
        assert($gateway instanceof MigrationsTableGateway);

        return new self($migration, $executor, $gateway);
    }

    public function isUp(MigrationInterface $migration): bool
    {
        return $this->gateway->select(['selector' => $migration->selector()])->count() > 0;
    }

    public function markUp(MigrationInterface $migration): void
    {
        $this->gateway->insert([
            'selector' => $migration->selector(),
        ]);
    }
}
