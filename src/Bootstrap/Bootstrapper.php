<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Handler\PingHandler;
use GlobIterator;
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\Diactoros\ConfigProvider as LaminasDiactorosConfigProvider;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stratigility\Middleware\ErrorHandler;
use LogicException;
use Mezzio\Application;
use Mezzio\ConfigProvider as MezioConfigProvider;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\ConfigProvider as MezzioRouterConfigProvider;
use Mezzio\Router\FastRouteRouter\ConfigProvider as MezzioRouterFastRouteRouterConfigProvider;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Container\ContainerInterface;

use function assert;
use function getenv;
use function is_array;
use function is_string;

final readonly class Bootstrapper
{
    public static function bootstrap(): Kernel
    {
        /**
         * @phpstan-ignore-next-line argument.type
         */
        $container = new ServiceManager(self::cached());

        $app = $container->get(Application::class);
        $factory = $container->get(MiddlewareFactory::class);

        assert($app instanceof Application);
        assert($factory instanceof MiddlewareFactory);

        self::pipeline($app, $factory, $container);
        self::routes($app, $factory, $container);

        return new Kernel($app, $container);
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function cached(): array
    {
        $cache = new ApcuConfigCache();

        $cached = $cache->get();

        if ($cached !== null) {
            return $cached;
        }

        $fresh = self::config();

        $cache->set($fresh);

        return $fresh;
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function config(): array
    {
        (new IniPutEnv(new GlobIterator(__DIR__ . '/../../.env.ini')))();

        $env = getenv('APP_ENV');

        if (!is_string($env)) {
            throw new LogicException('APP_ENV');
        }

        $config = (new ConfigAggregator([
            ConfigProvider::class,
            LaminasDiactorosConfigProvider::class,
            MezioConfigProvider::class,
            MezzioRouterConfigProvider::class,
            MezzioRouterFastRouteRouterConfigProvider::class,
            new PhpFileProvider(__DIR__ . '/../../config/global.php'),
            new PhpFileProvider(__DIR__ . '/../../.env.php'),
            new PhpFileProvider(__DIR__ . '/../../config/' . $env . '.php'),
            new PhpFileProvider(__DIR__ . '/../../.env.' . $env . '.php'),
        ]))->getMergedConfig();

        $dependencies = (new ConfigAggregator([
            new ArrayProvider($config),
            new ArrayProvider([
                'dependencies' => [
                    'services' => [
                        'config' => $config,
                    ],
                ],
            ]),
        ]))->getMergedConfig()['dependencies'] ?? [];

        assert(is_array($dependencies));

        return $dependencies;
    }

    private static function pipeline(Application $app, MiddlewareFactory $factory, ContainerInterface $container): void
    {
        $app->pipe(ErrorHandler::class);
        $app->pipe(RouteMiddleware::class);
        $app->pipe(MethodNotAllowedMiddleware::class);
        $app->pipe(DispatchMiddleware::class);
        $app->pipe(NotFoundHandler::class);
    }

    private static function routes(Application $app, MiddlewareFactory $factory, ContainerInterface $container): void
    {
        $app->route(PingHandler::PATH, [PingHandler::class], [PingHandler::METHOD]);
    }
}
