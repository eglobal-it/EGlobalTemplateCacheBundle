<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Cache;

use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;

interface TemplateCacheInterface
{
    public function writeTemplate(CacheableTemplate $cacheableTemplate, string $locale): string;

    public function writeMap(array $templateMap, string $locale): string;
}
