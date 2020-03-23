<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Twig\Extension;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateCacheExtension extends AbstractExtension
{
    private $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('jsTemplateMapFileName', [$this, 'getTemplateMapFileNameLocale']),
        ];
    }

    /**
     * @return string|null
     */
    public function getTemplateMapFileNameLocale(string $locale)
    {
        foreach ((new Finder())->files()->in($this->cacheDir)->name($locale . '-*.js')->sort(function (SplFileInfo $a, SplFileInfo $b) {
            return $b->getMTime() - $a->getMTime();
        }) as $file) {
            return $file->getBasename();
        }

        return null;
    }

    public function getName(): string
    {
        return 'eglobal_template_cache_extension';
    }
}
