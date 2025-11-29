<?php

declare(strict_types=1);

namespace App\Migrator;

use Laminas\Db\Sql\SqlInterface;

interface MigrationInterface
{
    /**
     * @return iterable<int|string, SqlInterface|string>
     */
    public function down(): iterable;

    public function selector(): string;

    /**
     * @return iterable<int|string, SqlInterface|string>
     */
    public function up(): iterable;
}
