<?php

declare(strict_types=1);

namespace App\Migrator;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\SqlInterface;
use Psr\Container\ContainerInterface;

use function assert;

use const PHP_EOL;

final readonly class MigratorExecutor
{
    private readonly Adapter $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public static function factory(ContainerInterface $container): self
    {
        $adapter = $container->get(Adapter::class);

        assert($adapter instanceof Adapter);

        return new self($adapter);
    }

    public function execute(SqlInterface|string $ddl): void
    {
        if ($ddl instanceof SqlInterface) {
            $ddl = (new Sql($this->adapter))->buildSqlString($ddl);
        }

        echo 'Executing DDL: ' . $ddl . PHP_EOL;

        $this->adapter->query($ddl, Adapter::QUERY_MODE_EXECUTE);
    }
}
