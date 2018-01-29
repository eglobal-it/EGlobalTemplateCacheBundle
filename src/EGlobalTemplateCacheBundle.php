<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle;

use EGlobal\Bundle\TemplateCacheBundle\DependencyInjection\EGlobalTemplateCacheExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EGlobalTemplateCacheBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new EGlobalTemplateCacheExtension('eglobal_template_cache');
    }
}
