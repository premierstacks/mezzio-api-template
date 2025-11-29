<?php

declare(strict_types=1);

namespace Public;

use App\Bootstrap\Bootstrapper;

use function chdir;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../');

Bootstrapper::bootstrap()->app->run();
