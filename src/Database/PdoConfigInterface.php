<?php

declare(strict_types=1);

namespace App\Database;

interface PdoConfigInterface
{
    public string $dbname { get; }

    public string $host { get; }

    /**
     * @var array<int|string, mixed>
     */
    public array $options { get; }

    public string $password { get; }

    public string $port { get; }

    public string $socket { get; }

    public string $username { get; }

    /**
     * @param array<int|string, mixed> $with
     */
    public function clone(array $with): static;
}
