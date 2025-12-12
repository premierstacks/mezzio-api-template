<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function assert;

final readonly class Migrator
{
    private readonly PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function provide(ContainerInterface $container): self
    {
        $pdo = $container->get(PDO::class);

        assert($pdo instanceof PDO);

        return new self($pdo);
    }

    /**
     * @param iterable<int|string, MigrationInterface> $migrations
     */
    public function forward(iterable $migrations): void
    {
        $this->lock();

        try {
            $this->init();

            foreach ($migrations as $migration) {
                if ($this->isUp($migration)) {
                    continue;
                }

                foreach ($migration->up() as $ddl) {
                    $this->execute($ddl);
                }

                $this->markUp($migration);
            }
        } finally {
            $this->unlock();
        }
    }

    public function isUp(MigrationInterface $migration): bool
    {
        $stm = $this->pdo->prepare('select distinct 1 from migrations where selector = ? limit ?');

        assert($stm instanceof PDOStatement);

        $stm->execute([$migration->selector(), 1]);

        return $stm->fetchColumn() !== false;
    }

    public function markUp(MigrationInterface $migration): void
    {
        $stm = $this->pdo->prepare('insert into migrations (selector) values (?)');

        assert($stm instanceof PDOStatement);

        $stm->execute([$migration->selector()]);
    }

    private function execute(string $ddl): void
    {
        $stm = $this->pdo->prepare($ddl);

        assert($stm instanceof PDOStatement);

        $stm->execute();
    }

    private function init(): void
    {
        $this->execute(<<<'EOF'
            CREATE TABLE IF NOT EXISTS `migrations` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `selector` VARCHAR(255) NOT NULL UNIQUE,
              `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
              `updated_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
            EOF);
    }

    private function lock(): void
    {
        $stm = $this->pdo->prepare('SELECT GET_LOCK(?, ?)');

        assert($stm instanceof PDOStatement);

        $stm->execute(['migrations', 30]);

        if ($stm->fetchColumn() !== 1) {
            throw new RuntimeException();
        }
    }

    private function unlock(): void
    {
        $stm = $this->pdo->prepare('SELECT RELEASE_LOCK(?)');

        assert($stm instanceof PDOStatement);

        $stm->execute(['migrations']);
    }
}
