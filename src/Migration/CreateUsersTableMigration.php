<?php

declare(strict_types=1);

namespace App\Migration;

use App\Migrator\MigrationInterface;
use Override;
use Psr\Container\ContainerInterface;

final readonly class CreateUsersTableMigration implements MigrationInterface
{
    public function __construct() {}

    public static function factory(ContainerInterface $container): self
    {
        return new self();
    }

    #[Override]
    public function down(): iterable
    {
        yield from [];
    }

    #[Override]
    public function selector(): string
    {
        return self::class;
    }

    #[Override]
    public function up(): iterable
    {
        yield <<<'EOF'
            CREATE TABLE IF NOT EXISTS `users` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
            EOF;
    }
}
