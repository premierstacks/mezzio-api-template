<?php

declare(strict_types=1);

namespace App\Migrator;

use Psr\Container\ContainerInterface;

use function assert;

final readonly class Migrator
{
    private readonly MigratorExecutor $executor;

    private readonly MigratorMarker $marker;

    public function __construct(MigratorMarker $marker, MigratorExecutor $executor)
    {
        $this->marker = $marker;
        $this->executor = $executor;
    }

    public static function factory(ContainerInterface $container): self
    {
        $marker = $container->get(MigratorMarker::class);
        $executor = $container->get(MigratorExecutor::class);

        assert($marker instanceof MigratorMarker);
        assert($executor instanceof MigratorExecutor);

        return new self($marker, $executor);
    }

    /**
     * @param iterable<int|string, MigrationInterface> $migrations
     */
    public function forward(iterable $migrations): void
    {
        foreach ($migrations as $migration) {
            if ($this->marker->isUp($migration)) {
                continue;
            }

            foreach ($migration->up() as $statement) {
                $this->executor->execute($statement);
            }

            $this->marker->markUp($migration);
        }
    }
}
