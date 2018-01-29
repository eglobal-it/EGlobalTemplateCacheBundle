<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Dumper;

use EGlobal\Bundle\TemplateCacheBundle\Cache\TemplateCacheInterface;
use EGlobal\Bundle\TemplateCacheBundle\Finder\CacheableTemplateFinderInterface;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;
use Psr\Log\LoggerInterface;

class TemplateFileDumper implements TemplateFileDumperInterface
{
    private $templateFinder;
    private $templateCache;
    private $locales;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(CacheableTemplateFinderInterface $templateFinder, TemplateCacheInterface $templateCache, array $locales)
    {
        $this->templateFinder = $templateFinder;
        $this->templateCache = $templateCache;
        $this->locales = $locales;
    }

    public function dump()
    {
        /** @var CacheableTemplate[] $cacheableTemplates */
        $cacheableTemplates = $this->templateFinder->find();
        foreach ($this->locales as $locale) {
            $templateMap = [];

            foreach ($cacheableTemplates as $cacheableTemplate) {
                $templateMap[$cacheableTemplate->getRouteName()] = $this->dumpTemplate($cacheableTemplate, $locale);
            }

            $this->dumpTemplateMap($templateMap, $locale);
        }
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function dumpTemplate(CacheableTemplate $template, string $locale): string
    {
        $cachedUrl = $this->templateCache->writeTemplate($template, $locale);
        if ($this->logger) {
            $this->logger->info(sprintf(' - %s -> %s', $template->getMethodName(), $cachedUrl));
        }

        return $cachedUrl;
    }

    protected function dumpTemplateMap(array $templateMap, string $locale): string
    {
        $templateMapFileUrl = $this->templateCache->writeMap($templateMap, $locale);
        if ($this->logger) {
            $this->logger->info(sprintf(' [%s] Dumped %d templates. Map path %s', strtoupper($locale), count($templateMap), $templateMapFileUrl));
        }

        return $templateMapFileUrl;
    }
}
