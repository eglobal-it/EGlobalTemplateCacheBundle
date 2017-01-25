<?php

declare(strict_types = 1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Fixtures;

use EGlobal\Bundle\TemplateCacheBundle\EGlobalTemplateCacheBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $tempDir;

    public function __construct()
    {
        parent::__construct('test', true);
        $this->tempDir = __DIR__ . '/temp';
    }

    public function getCacheDir(): string
    {
        return $this->tempDir . '/cache';
    }

    public function getLogDir(): string
    {
        return $this->tempDir . '/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new AsseticBundle(),
            new TwigBundle(),
            new EGlobalTemplateCacheBundle(),
        ];
    }
}
