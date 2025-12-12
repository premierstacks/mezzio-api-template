<?php

declare(strict_types=1);

namespace Config;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use PDO;

return (new ConfigAggregator([
    new ArrayProvider([
        PDO::class => [
            'dbname' => 'local',
            'password' => '',
            'username' => 'local',
        ],
        'debug' => true,
    ]),
]))->getMergedConfig();
