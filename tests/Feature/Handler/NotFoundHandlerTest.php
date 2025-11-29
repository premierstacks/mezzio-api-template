<?php

declare(strict_types=1);

namespace Tests\Feature\Handler;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @internal
 */
#[CoversNothing]
#[Small]
final class NotFoundHandlerTest extends TestCase
{
    #[Test]
    public function test(): void
    {
        $response = $this->handle($this->createServerRequest(RequestMethodInterface::METHOD_GET, '/not-found'));

        self::assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
