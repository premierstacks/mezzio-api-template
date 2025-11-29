<?php

declare(strict_types=1);

namespace App\Factory;

use Laminas\Db\Adapter\Adapter;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

final readonly class AdapterInterfaceFactory
{
    public static function factory(ContainerInterface $container): Adapter
    {
        $config = $container->get('config');

        assert(is_array($config));

        $db = $config['db'] ?? null;

        assert(is_array($db));

        return new Adapter($db);
    }
}
