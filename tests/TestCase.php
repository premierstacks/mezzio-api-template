<?php

declare(strict_types=1);

namespace Tests;

use App\Bootstrap\Bootstrapper;
use App\Bootstrap\Kernel;
use App\Database\Migrations;
use App\Database\Migrator;
use App\Database\PdoConfigInterface;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Override;
use PDO;
use PHPUnit\Framework\TestCase as VendorTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Random\Randomizer;

use function assert;
use function implode;
use function range;

/**
 * @internal
 */
abstract class TestCase extends VendorTestCase
{
    private string $id = '';

    private Kernel|null $kernel = null;

    private bool $migrated = false;

    protected function app(): Application
    {
        return $this->kernel()->app;
    }

    protected function container(): ServiceManager
    {
        return $this->kernel()->container;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    protected function createServerRequest(string $method, UriInterface|string $uri, array $params = []): ServerRequestInterface
    {
        return $this->resolve(ServerRequestFactoryInterface::class)->createServerRequest($method, $uri, $params);
    }

    protected function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->app()->handle($request);
    }

    protected function kernel(): Kernel
    {
        if ($this->kernel === null) {
            $this->kernel = Bootstrapper::bootstrap();
        }

        return $this->kernel;
    }

    protected function migrate(): void
    {
        $this->migrated = true;

        $pdo = $this->pdo();

        $pdo->exec("DROP DATABASE IF EXISTS `{$this->id}`");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->id}` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");

        $container = $this->container();
        $config = $container->get(PdoConfigInterface::class);

        assert($config instanceof PdoConfigInterface);

        $override = $config->clone([
            'dbname' => $this->id,
        ]);

        $container->setAllowOverride(true);
        $container->setService(PDO::class, null);
        $container->setService(PdoConfigInterface::class, $override);
        $container->setAllowOverride(false);

        $this->resolve(Migrator::class)->forward($this->resolve(Migrations::class));
    }

    protected function pdo(): PDO
    {
        return $this->resolve(PDO::class);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    protected function resolve(string $class): object
    {
        $resolved = $this->container()->get($class);

        assert($resolved instanceof $class);

        return $resolved;
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->id = (new Randomizer())->getBytesFromString(implode('', range('a', 'z') + range('A', 'Z') + range('0', '9')), 32);
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->migrated) {
            $this->pdo()->exec("DROP DATABASE IF EXISTS `{$this->id}`");
        }
    }
}
