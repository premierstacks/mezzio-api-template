<?php

declare(strict_types=1);

namespace App\Database;

use App\Migration\CreateUsersTableMigration;
use IteratorAggregate;
use Override;
use Psr\Container\ContainerInterface;
use Traversable;

use function assert;

/**
 * @implements IteratorAggregate<int|string, MigrationInterface>
 */
final readonly class Migrations implements IteratorAggregate
{
    private readonly ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function provide(ContainerInterface $container): self
    {
        return new self($container);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        foreach (self::migrations() as $class) {
            $instance = $this->container->get($class);

            assert($instance instanceof MigrationInterface);

            yield $instance;
        }
    }

    /**
     * @return iterable<int|string, class-string<MigrationInterface>>
     */
    private static function migrations(): iterable
    {
        yield CreateUsersTableMigration::class;
    }
}
