<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Cache;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;

class FilesystemCache implements TemplateCacheInterface
{
    const TEMPLATE_FILENAME_TEMPLATE = '%locale%/%hash%.%extension%';
    const MAP_FILENAME_TEMPLATE = '%locale%-%hash%.js';

    private $engine;
    private $translator;
    private $cacheDir;
    private $publicPrefix;

    public function __construct(EngineInterface $engine, TranslatorInterface $translator, string $cacheDir, string $publicPrefix = '')
    {
        $this->engine = $engine;
        $this->translator = $translator;
        $this->cacheDir = $cacheDir;
        $this->publicPrefix = $publicPrefix;
    }

    public function writeTemplate(CacheableTemplate $cacheableTemplate, string $locale): string
    {
        $originalLocale = $this->translator->getLocale();
        $this->translator->setLocale($locale);

        try {
            $content = $this->engine->render($cacheableTemplate->getTemplate(), $cacheableTemplate->getTemplateVars());
            $relativePath = $this->getTemplateRelativePath($content, $locale, $this->resolveTemplateExtension($cacheableTemplate->getTemplate()));
            $this->writeToFile($relativePath, $content);

            return $this->getPublicPath($relativePath);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->translator->setLocale($originalLocale);
        }
    }

    public function writeMap(array $templateMap, string $locale): string
    {
        $mapContent = $this->engine->render('@EGlobalTemplateCache/getTemplates.js.twig', [
            'templates' => $templateMap,
        ]);

        $relativePath = $this->getTemplateMapRelativePath($locale, $mapContent);
        $this->writeToFile($relativePath, $mapContent);

        return $this->getPublicPath($relativePath);
    }

    private function getTemplateRelativePath(string $content, string $locale, string $extension): string
    {
        return strtr(self::TEMPLATE_FILENAME_TEMPLATE, [
            '%locale%' => trim($locale, '/'),
            '%hash%' => md5($content),
            '%extension%' => $extension,
        ]);
    }

    private function getTemplateMapRelativePath(string $locale, string $mapContent)
    {
        return strtr(self::MAP_FILENAME_TEMPLATE, [
            '%locale%' => trim($locale, '/'),
            '%hash%' => md5($mapContent),
        ]);
    }

    protected function resolveTemplateExtension(string $templatePath): string
    {
        $parts = explode('.', $templatePath);
        array_pop($parts);
        $extension = array_pop($parts);

        return $extension;
    }

    private function writeToFile(string $relativePath, string $content)
    {
        $fullPath = $this->getAbsolutePath($relativePath);
        $directory = dirname($fullPath);

        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Unable to create directory "%s".', $directory));
        }

        if (false === file_put_contents($fullPath, $content)) {
            throw new \RuntimeException(sprintf('Unable to create file "%s".', $fullPath));
        }
    }

    private function getAbsolutePath(string $relativePath): string
    {
        return sprintf('/%s/%s', trim($this->cacheDir, '/'), $relativePath);
    }

    private function getPublicPath(string $relativePath): string
    {
        return sprintf('%s/%s', rtrim($this->publicPrefix, '/'), $relativePath);
    }
}
