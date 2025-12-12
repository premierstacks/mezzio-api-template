<?php

declare(strict_types=1);

namespace App\Provider;

use App\Database\PdoConfigInterface;
use PDO;
use Pdo\Mysql;
use Psr\Container\ContainerInterface;

use function array_filter;
use function array_replace;
use function assert;
use function implode;

final readonly class PdoProvider
{
    public static function provide(ContainerInterface $container): PDO
    {
        $config = $container->get(PdoConfigInterface::class);

        assert($config instanceof PdoConfigInterface);

        $dsn = [
            $config->host !== '' ? ('host=' . $config->host) : '',
            $config->port !== '' ? ('port=' . $config->port) : '',
            $config->dbname !== '' ? ('dbname=' . $config->dbname) : '',
            $config->socket !== '' ? ('unix_socket=' . $config->socket) : '',
            'charset=utf8mb4',
        ];

        return new Mysql('mysql:' . implode(';', array_filter($dsn, static fn(string $v): bool => $v !== '')), $config->username, $config->password, array_replace([
            Mysql::ATTR_DEFAULT_FETCH_MODE => Mysql::FETCH_ASSOC,
            Mysql::ATTR_EMULATE_PREPARES => false,
            Mysql::ATTR_ERRMODE => Mysql::ERRMODE_EXCEPTION,
            Mysql::ATTR_MULTI_STATEMENTS => false,
            Mysql::ATTR_INIT_COMMAND => 'SET SESSION time_zone = \'+00:00\'',
        ], $config->options));
    }
}
