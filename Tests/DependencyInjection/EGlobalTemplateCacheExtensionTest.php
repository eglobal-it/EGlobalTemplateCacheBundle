<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\DependencyInjection;

use EGlobal\Bundle\TemplateCacheBundle\DependencyInjection\EGlobalTemplateCacheExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class EGlobalTemplateCacheExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_registers_service_parameters()
    {
        $this->load([
            'locales' => ['ru', 'en', 'es'],
            'cache_dir' => '/cache/foo/bar',
            'public_prefix' => '/public/foo',
            'root_dirs' => [
                '/foo/bar',
                'bar/foo',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('eglobal_template_cache.locales', ['ru', 'en', 'es']);
        $this->assertContainerBuilderHasParameter('eglobal_template_cache.root_dirs', ['/foo/bar', 'bar/foo']);
        $this->assertContainerBuilderHasParameter('eglobal_template_cache.public_prefix', '/public/foo');
        $this->assertContainerBuilderHasParameter('eglobal_template_cache.cache_dir', '/cache/foo/bar');
        $this->assertContainerBuilderHasParameter('eglobal_template_cache.exposed_routes_only', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new EGlobalTemplateCacheExtension('eglobal_template_cache'),
        ];
    }
}
