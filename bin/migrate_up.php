<?php

declare(strict_types=1);

namespace Bin;

use App\Bootstrap\Bootstrapper;
use App\Database\Migrations;
use App\Database\Migrator;

use function assert;
use function chdir;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../');

$kernel = Bootstrapper::bootstrap();

$migrations = $kernel->container->get(Migrations::class);
$migrator = $kernel->container->get(Migrator::class);

assert($migrations instanceof Migrations);
assert($migrator instanceof Migrator);

$migrator->forward($migrations);
