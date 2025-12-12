<?php

declare(strict_types=1);

namespace App\Handler;

use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function time;

final readonly class PingHandler implements RequestHandlerInterface
{
    public const string METHOD = RequestMethodInterface::METHOD_GET;

    public const string PATH = '/api/ping';

    public function __construct() {}

    public static function provide(ContainerInterface $container): self
    {
        return new self();
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'meta' => [
                'timestamp' => time(),
            ],
        ]);
    }
}
