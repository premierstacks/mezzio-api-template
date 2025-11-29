<?php

declare(strict_types=1);

namespace Config;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

use function getenv;

return (new ConfigAggregator([
    new ArrayProvider([
        'APP_ENV' => getenv('APP_ENV'),
        'db' => [
            'database' => getenv('DB_DATABASE'),
            'driver' => 'Pdo_Mysql',
            'password' => getenv('DB_PASSWORD'),
            'username' => getenv('DB_USER'),
        ],
        'debug' => false,
    ]),
]))->getMergedConfig();
