<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Finder;

use Doctrine\Common\Annotations\Reader;
use EGlobal\Bundle\TemplateCacheBundle\Annotation\CacheableTemplate;
use EGlobal\Bundle\TemplateCacheBundle\Helper\FileContentTokenizer;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate as CacheableTemplateModel;
use ReflectionMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CacheableTemplateFinder implements CacheableTemplateFinderInterface
{
    private $rootDirs;
    private $annotationReader;
    private $finder;
    private $exposedRoutesOnly;

    public function __construct(Reader $reader, FileLocatorInterface $fileLocator, array $rootDirs, bool $exposedRoutesOnly)
    {
        $this->annotationReader = $reader;
        $this->finder = new Finder();
        $this->exposedRoutesOnly = $exposedRoutesOnly;
        $this->rootDirs = [];

        foreach ($rootDirs as $dir) {
            array_push($this->rootDirs, $fileLocator->locate($dir));
        }
    }

    public function find(): array
    {
        $cacheableTemplates = [];

        foreach ($this->finder->files()->in($this->rootDirs)->name('*Controller.php') as $file) {
            $cacheableTemplates = array_merge($cacheableTemplates, $this->processFile($file));
        }

        return $cacheableTemplates;
    }

    protected function processFile(SplFileInfo $file): array
    {
        $cacheableTemplates = [];
        $className = FileContentTokenizer::getFullyQualifiedClassName($file->getContents());
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            /* @var $templateAnnotation CacheableTemplate */
            if (null === $templateAnnotation = $this->annotationReader->getMethodAnnotation($method, CacheableTemplate::class)) {
                continue;
            }

            $template = (is_string($templateAnnotation->getTemplate())) ? $templateAnnotation->getTemplate() : $templateAnnotation->getTemplate()->getPath();
            $templateVars = $templateAnnotation->getVars();

            if (null === $routeName = $this->resolveRouteName($method)) {
                throw new \RuntimeException(sprintf('Could not resolve route name of %s::%s. Please, check if it has an exposed route.', $className, $method->getName()));
            }

            array_push($cacheableTemplates, new CacheableTemplateModel(
                sprintf('%s::%s', $className, $method->getName()),
                $routeName,
                $template,
                $templateVars
            ));
        }

        return $cacheableTemplates;
    }

    /**
     * @return string|null
     */
    private function resolveRouteName(ReflectionMethod $method)
    {
        $routeAnnotations = array_filter($this->annotationReader->getMethodAnnotations($method), function ($annotation) {
            return ($annotation instanceof Route) && $annotation->getName();
        });

        foreach ($routeAnnotations as $routeAnnotation) {
            /* @var Route $routeAnnotation */
            $options = $routeAnnotation->getOptions();

            if ($this->exposedRoutesOnly && (!array_key_exists('expose', $options) || !$options['expose'])) {
                continue;
            }

            if (null !== $routeName = $routeAnnotation->getName()) {
                return $routeName;
            }
        }

        return null;
    }
}
