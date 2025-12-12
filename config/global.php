<?php

declare(strict_types=1);

namespace Config;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use PDO;

use function getenv;

return (new ConfigAggregator([
    new ArrayProvider([
        'APP_ENV' => getenv('APP_ENV'),
        PDO::class => [
            'dbname' => getenv('DB_DATABASE'),
            'password' => getenv('DB_PASSWORD'),
            'username' => getenv('DB_USER'),
            'options' => [],
            'host' => '',
            'port' => '',
            'socket' => '',
        ],
        'debug' => false,
    ]),
]))->getMergedConfig();
