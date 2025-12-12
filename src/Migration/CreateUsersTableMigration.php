<?php

declare(strict_types=1);

namespace App\Migration;

use App\Database\MigrationInterface;
use Override;
use Psr\Container\ContainerInterface;

final readonly class CreateUsersTableMigration implements MigrationInterface
{
    public function __construct() {}

    public static function provide(ContainerInterface $container): self
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
              `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              `updated_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
            EOF;
    }
}
