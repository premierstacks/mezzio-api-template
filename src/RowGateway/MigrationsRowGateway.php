<?php

declare(strict_types=1);

namespace App\RowGateway;

use Laminas\Db\RowGateway\RowGateway;

/**
 * @property string $created_at
 * @property int $id
 * @property string $selector
 */
final class MigrationsRowGateway extends RowGateway {}
