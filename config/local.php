<?php

declare(strict_types=1);

namespace Config;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

return (new ConfigAggregator([
    new ArrayProvider([
        'db' => [
            'database' => 'local',
            'password' => null,
            'username' => 'local',
        ],
        'debug' => true,
    ]),
]))->getMergedConfig();
