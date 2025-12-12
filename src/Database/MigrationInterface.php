<?php

declare(strict_types=1);

namespace App\Database;

interface MigrationInterface
{
    /**
     * @return iterable<int|string, string>
     */
    public function down(): iterable;

    public function selector(): string;

    /**
     * @return iterable<int|string, string>
     */
    public function up(): iterable;
}
