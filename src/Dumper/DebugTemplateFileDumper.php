<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Dumper;

use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;

class DebugTemplateFileDumper extends TemplateFileDumper
{
    protected function dumpTemplate(CacheableTemplate $template, string $locale): string
    {
        if ($this->logger) {
            $this->logger->info(sprintf(' - %s: Processing %s', $locale, $template->getMethodName()));
        }

        return $template->getTemplate();
    }

    protected function dumpTemplateMap(array $templateMap, string $locale): string
    {
        if ($this->logger) {
            $this->logger->info(sprintf(' [%s] %d templates will be added to map.', strtoupper($locale), count($templateMap)));
        }

        return $locale . '.js';
    }
}
