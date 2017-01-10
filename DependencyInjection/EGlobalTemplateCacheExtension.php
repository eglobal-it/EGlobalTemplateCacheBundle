<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class EGlobalTemplateCacheExtension extends ConfigurableExtension
{
    private $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration($this->getAlias());
    }

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('eglobal_template_cache.locales', $mergedConfig['locales']);
        $container->setParameter('eglobal_template_cache.root_dirs', $mergedConfig['root_dirs']);
        $container->setParameter('eglobal_template_cache.cache_dir', $mergedConfig['cache_dir']);
        $container->setParameter('eglobal_template_cache.public_prefix', $mergedConfig['public_prefix']);
        $container->setParameter('eglobal_template_cache.exposed_routes_only', $mergedConfig['exposed_routes_only']);
    }
}
