<?php

declare(strict_types=1);

namespace Tests;

use App\Bootstrap\Bootstrapper;
use App\Bootstrap\Kernel;
use App\Migrator\Migrations;
use App\Migrator\Migrator;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Override;
use PHPUnit\Framework\TestCase as VendorTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Random\Randomizer;

use function array_replace;
use function assert;
use function implode;
use function is_array;
use function range;

/**
 * @internal
 */
abstract class TestCase extends VendorTestCase
{
    private string $id = '';

    private Kernel|null $kernel = null;

    private bool $migrated = false;

    protected function adapter(): Adapter
    {
        return $this->resolve(Adapter::class);
    }

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

        $adapter = $this->adapter();

        $adapter->getDriver()->createStatement("DROP DATABASE IF EXISTS `{$this->id}`")->execute();
        $adapter->getDriver()->createStatement("CREATE DATABASE IF NOT EXISTS `{$this->id}` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci")->execute();

        $container = $this->container();

        $config = $container->get('config');

        assert(is_array($config) && isset($config['db']) && is_array($config['db']));

        $container->setAllowOverride(true);

        $override = new Adapter(array_replace($config['db'], [
            'database' => $this->id,
        ]));

        $container->setService(AdapterInterface::class, $override);
        $container->setService(Adapter::class, $override);

        $container->setAllowOverride(false);

        $this->resolve(Migrator::class)->forward($this->resolve(Migrations::class));
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
            $this->adapter()->getDriver()->createStatement("DROP DATABASE IF EXISTS `{$this->id}`")->execute();
        }
    }
}
