<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;

final readonly class Kernel
{
    public readonly Application $app;

    public readonly ServiceManager $container;

    public function __construct(Application $app, ServiceManager $container)
    {
        $this->app = $app;
        $this->container = $container;
    }
}
