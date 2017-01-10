<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Dumper;

use Psr\Log\LoggerInterface;
use EGlobal\Bundle\TemplateCacheBundle\Cache\TemplateCacheInterface;
use EGlobal\Bundle\TemplateCacheBundle\Dumper\TemplateFileDumper;
use EGlobal\Bundle\TemplateCacheBundle\Finder\CacheableTemplateFinderInterface;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;

class TemplateFileDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheableTemplateFinderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateFinder;

    /**
     * @var TemplateCacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateCache;

    /**
     * @var TemplateFileDumper
     */
    protected $dumper;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected $locales = ['ru', 'en'];

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->templateFinder = $this->getMock(CacheableTemplateFinderInterface::class);
        $this->templateCache = $this->getMock(TemplateCacheInterface::class);
        $this->logger = $this->getMock(LoggerInterface::class);

        $this->dumper = new TemplateFileDumper($this->templateFinder, $this->templateCache, $this->locales);
        $this->dumper->setLogger($this->logger);
    }

    /**
     * @test
     */
    public function it_dumps_templates()
    {
        $foundTemplates = $this->generateFoundTemplates(3);

        $methodIteratorIndex = 0;
        foreach ($this->locales as $locale) {
            $templateMap = [];

            foreach ($foundTemplates as $templateIndex => $template) {
                $templateMap[sprintf('test.foo.route.%d', $templateIndex + 1)] = sprintf('/public/foo/%s/%d.html', $locale, $templateIndex + 1);

                $this->templateCache
                    ->expects($this->at($methodIteratorIndex))
                    ->method('writeTemplate')
                    ->with($template, $locale)
                    ->willReturn(sprintf('/public/foo/%s/%d.html', $locale, $templateIndex + 1));

                $this->logger
                    ->expects($this->at($methodIteratorIndex))
                    ->method('info')
                    ->with(sprintf(' - Foo\\Bar\\Test::template%1$dAction -> /public/foo/%2$s/%1$d.html', $templateIndex + 1, $locale));

                ++$methodIteratorIndex;
            }

            $this->assertEquals(3, count($templateMap));

            $this->templateCache
                ->expects($this->at($methodIteratorIndex))
                ->method('writeMap')
                ->with($templateMap, $locale)
                ->willReturn(sprintf('/public/map-%s.js', $locale));

            $this->logger
                ->expects($this->at($methodIteratorIndex))
                ->method('info')
                ->with(sprintf(' [%s] Dumped 3 templates. Map path /public/map-%s.js', strtoupper($locale), $locale));

            ++$methodIteratorIndex;
        }

        $this->dumper->dump();
    }

    protected function generateFoundTemplates(int $count): array
    {
        $foundTemplates = [];
        for ($i = 1; $i <= $count; ++$i) {
            array_push($foundTemplates, $this->createCacheableTemplate(
                sprintf('test.foo.route.%d', $i),
                sprintf('@Foo:Bar/Test:template_%d.html.twig', $i),
                sprintf('Foo\\Bar\\Test::template%dAction', $i)
            ));
        }

        $this->templateFinder->expects($this->once())->method('find')->willReturn($foundTemplates);

        return $foundTemplates;
    }

    /**
     * @return CacheableTemplate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createCacheableTemplate(string $routeName, string $templateName, string $methodName)
    {
        $template = $this->getMockBuilder(CacheableTemplate::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRouteName', 'getTemplate', 'getMethodName'])
            ->getMock();

        $template->expects($this->any())->method('getRouteName')->willReturn($routeName);
        $template->expects($this->any())->method('getTemplate')->willReturn($templateName);
        $template->expects($this->any())->method('getMethodName')->willReturn($methodName);

        return $template;
    }
}
