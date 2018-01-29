<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Finder;

interface CacheableTemplateFinderInterface
{
    public function find(): array;
}
