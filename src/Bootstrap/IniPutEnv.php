<?php

declare(strict_types=1);

namespace App\Bootstrap;

use DirectoryIterator;
use LogicException;
use UnexpectedValueException;

use function getenv;
use function in_array;
use function is_string;
use function parse_ini_file;
use function putenv;

use const INI_SCANNER_RAW;

final readonly class IniPutEnv
{
    private readonly DirectoryIterator $files;

    /**
     * @var array<int|string, int|string>
     */
    private readonly array $keys;

    /**
     * @param array<int|string, string> $keys
     */
    public function __construct(DirectoryIterator $files, array $keys = [])
    {
        $this->files = $files;
        $this->keys = $keys;
    }

    public function __invoke(): void
    {
        foreach ($this->files as $file) {
            $parsed = parse_ini_file((string) $file, false, INI_SCANNER_RAW);

            if ($parsed === false) {
                throw new UnexpectedValueException('parse_ini_file');
            }

            foreach ($parsed as $key => $value) {
                if (!is_string($key) || !is_string($value)) {
                    throw new LogicException(self::class);
                }

                if ($this->keys === [] || in_array($key, $this->keys, true)) {
                    if (getenv($key) === false) {
                        putenv($key . '=' . $value);
                    }
                }
            }
        }
    }
}
