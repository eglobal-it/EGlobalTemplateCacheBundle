<?php

declare(strict_types=1);

namespace Tests\EGlobal\Bundle\TemplateCacheBundle\DependencyInjection;

use EGlobal\Bundle\TemplateCacheBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_tests_complete_configuration()
    {
        $config = [
            'locales' => ['ru', 'en', 'es'],
            'cache_dir' => '/cache/foo/bar',
            'public_prefix' => '/public/foo',
            'root_dirs' => [
                '/foo/bar',
                'bar/foo',
            ],
            'exposed_routes_only' => true,
        ];

        $this->assertProcessedConfigurationEquals([$config], $config);
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "locales" at path "eglobal_template_cache" must be configured.
     */
    public function it_requires_locales_configuration()
    {
        $config = [];

        $this->assertProcessedConfigurationEquals([$config], [], 'locales');
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "cache_dir" at path "eglobal_template_cache" must be configured.
     */
    public function it_requires_cache_dir_configuration()
    {
        $config = [];

        $this->assertProcessedConfigurationEquals([$config], [], 'cache_dir');
    }

    /**
     * @test
     */
    public function it_adds_default_public_prefix_null_value()
    {
        $this->assertProcessedConfigurationEquals([], ['public_prefix' => null], 'public_prefix');
    }

    /**
     * @test
     */
    public function it_adds_default_exposed_routes_false_value()
    {
        $this->assertProcessedConfigurationEquals([], ['exposed_routes_only' => false], 'exposed_routes_only');
    }

    /**
     * @test
     */
    public function it_adds_default_root_dirs_blank_array_value()
    {
        $this->assertProcessedConfigurationEquals([], ['root_dirs' => []], 'root_dirs');
    }

    /**
     * @test
     */
    public function it_splits_locales_into_array()
    {
        $config = [
            'locales' => 'ru,en,es',
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'locales' => ['ru', 'en', 'es'],
        ], 'locales');
    }

    /**
     * @test
     */
    public function it_converts_single_root_dirs_value_into_array()
    {
        $config = [
            'root_dirs' => '/foo/bar',
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'root_dirs' => ['/foo/bar'],
        ], 'root_dirs');
    }

    /**
     * @test
     */
    public function it_splits_root_dirs_into_array()
    {
        $config = [
            'root_dirs' => '/foo/bar,/bar/foo,/test/foo/bar',
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'root_dirs' => ['/foo/bar', '/bar/foo', '/test/foo/bar'],
        ], 'root_dirs');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration('eglobal_template_cache');
    }
}
