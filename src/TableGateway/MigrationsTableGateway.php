<?php

declare(strict_types=1);

namespace App\TableGateway;

use App\RowGateway\MigrationsRowGateway;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Laminas\Db\TableGateway\TableGateway;
use Psr\Container\ContainerInterface;

use function assert;

final class MigrationsTableGateway extends TableGateway
{
    public static function factory(ContainerInterface $container): self
    {
        $adapter = $container->get(AdapterInterface::class);
        $table = new TableIdentifier('migrations');

        assert($adapter instanceof AdapterInterface);

        return new self($table, $adapter, new RowGatewayFeature(new MigrationsRowGateway('id', $table, $adapter)));
    }
}
