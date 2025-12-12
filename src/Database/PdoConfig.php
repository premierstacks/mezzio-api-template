<?php

declare(strict_types=1);

namespace App\Database;

use Override;
use PDO;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_string;

final readonly class PdoConfig implements PdoConfigInterface
{
    #[Override]
    public readonly string $dbname;

    #[Override]
    public readonly string $host;

    /**
     * @var array<int|string, mixed>
     */
    #[Override]
    public readonly array $options;

    #[Override]
    public readonly string $password;

    #[Override]
    public readonly string $port;

    #[Override]
    public readonly string $socket;

    #[Override]
    public readonly string $username;

    /**
     * @param array<int|string, mixed> $options
     */
    public function __construct(string $host, string $port, string $dbname, string $socket, string $username, string $password, array $options)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->socket = $socket;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    public static function provide(ContainerInterface $container): self
    {
        $config = $container->get('config');

        assert(is_array($config));
        assert(isset($config[PDO::class]));
        assert(is_array($config[PDO::class]));

        $pdo = $config[PDO::class];

        assert(isset($pdo['host'], $pdo['port'], $pdo['dbname'], $pdo['socket'], $pdo['username'], $pdo['password'], $pdo['options']));
        assert(is_string($pdo['host']));
        assert(is_string($pdo['port']));
        assert(is_string($pdo['dbname']));
        assert(is_string($pdo['socket']));
        assert(is_string($pdo['username']));
        assert(is_string($pdo['password']));
        assert(is_array($pdo['options']));

        return new self($pdo['host'], $pdo['port'], $pdo['dbname'], $pdo['socket'], $pdo['username'], $pdo['password'], $pdo['options']);
    }

    #[Override]
    public function clone(array $with): static
    {
        return clone ($this, $with);
    }
}
