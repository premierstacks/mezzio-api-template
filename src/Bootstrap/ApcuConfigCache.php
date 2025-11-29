<?php

declare(strict_types=1);

namespace App\Bootstrap;

use UnexpectedValueException;

use function apcu_enabled;
use function apcu_fetch;
use function apcu_store;
use function is_array;

use const PHP_SAPI;

final readonly class ApcuConfigCache
{
    /**
     * @return array<int|string, mixed>|null
     */
    public function get(): array|null
    {
        if (!self::enabled()) {
            return null;
        }

        $ok = false;
        $resolved = apcu_fetch(self::key(), $ok);

        if ($ok && is_array($resolved)) {
            return $resolved;
        }

        return null;
    }

    /**
     * @param array<int|string, mixed> $config
     */
    public function set(array $config): void
    {
        if (!self::enabled()) {
            return;
        }

        $ok = apcu_store(self::key(), $config);

        if ($ok !== true) {
            throw new UnexpectedValueException('apcu_store');
        }
    }

    private static function enabled(): bool
    {
        return apcu_enabled() && PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server' && PHP_SAPI !== 'phpdbg';
    }

    private static function key(): string
    {
        return self::class;
    }
}
