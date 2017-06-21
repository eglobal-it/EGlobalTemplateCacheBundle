<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Cache;

use EGlobal\Bundle\TemplateCacheBundle\Cache\FilesystemCache;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FilesystemCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $engine;

    /**
     * @var TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var RequestContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $routerContext;

    private $cacheDir;
    private $publicPrefix;

    /**
     * @var FilesystemCache
     */
    private $cache;

    protected function setUp()
    {
        $this->engine = $this->getMockBuilder(EngineInterface::class)->getMock();
        $this->translator = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $this->routerContext = $this->getMockBuilder(RequestContext::class)->getMock();

        $this->router = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $this->router->expects($this->any())->method('getContext')->willReturn($this->routerContext);

        $this->cacheDir = sys_get_temp_dir() . '/templates';

        $this->publicPrefix = '/foo/public';

        $this->cache = new FilesystemCache($this->engine, $this->translator, $this->router, $this->cacheDir, $this->publicPrefix);
    }

    /**
     * @test
     */
    public function it_writes_template_to_file()
    {
        $cacheableTemplate = new CacheableTemplate(
            'Foo\Bar\TestController::indexAction',
            'foo.bar.test.index',
            '@Foo:Bar/Test:index.html.twig',
            [
                'foo' => 'bar',
            ]
        );

        $templateContent = '<h1>Hello world!</h1>';

        $this->engine
            ->expects($this->once())->method('render')->with('@Foo:Bar/Test:index.html.twig', ['foo' => 'bar'])
            ->willReturn($templateContent);

        $this->translator->expects($this->once())->method('getLocale')->willReturn('en');
        $this->translator->expects($this->exactly(2))->method('setLocale')
            ->withConsecutive(['ru'], ['en']);

        $this->router->expects($this->exactly(2))->method('setContext');
        $this->routerContext
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(['_locale', 'ru'], ['_locale', 'en'])
        ;

        $publicPath = $this->cache->writeTemplate($cacheableTemplate, 'ru');
        $this->assertEquals('/foo/public/ru/fb80156296e1e8f20637d9ef870e0bb6.html', $publicPath);
        $cachedFilePath = sprintf('%s/ru/fb80156296e1e8f20637d9ef870e0bb6.html', $this->cacheDir);
        $this->assertFileExists($cachedFilePath);
        $this->assertEquals($templateContent, file_get_contents($cachedFilePath));
        $this->assertTrue(unlink($cachedFilePath));
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Test template engine exception
     */
    public function it_reverts_translator_locale_on_template_engine_exception()
    {
        $cacheableTemplate = new CacheableTemplate(
            'Foo\Bar\TestController::indexAction',
            'foo.bar.test.index',
            '@Foo:Bar/Test:index.html.twig',
            [
                'foo' => 'bar',
            ]
        );

        $this->engine->expects($this->once())->method('render')->willThrowException(new \Exception('Test template engine exception'));
        $this->translator->expects($this->once())->method('getLocale')->willReturn('en');
        $this->translator->expects($this->exactly(2))->method('setLocale')->withConsecutive(['ru'], ['en']);
        $this->router->expects($this->exactly(2))->method('setContext');
        $this->routerContext
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(['_locale', 'ru'], ['_locale', 'en'])
        ;
        $this->cache->writeTemplate($cacheableTemplate, 'ru');
    }

    /**
     * @test
     */
    public function it_writes_template_map_to_file()
    {
        $templateContent = '<h1>Hello templates map!</h1>';

        $this->engine
            ->expects($this->once())->method('render')->with('@EGlobalTemplateCache/getTemplates.js.twig', ['templates' => ['foo' => 'bar']])
            ->willReturn($templateContent);

        $publicPath = $this->cache->writeMap(['foo' => 'bar'], 'ru');
        $this->assertEquals('/foo/public/ru-13df9bdf8e0c78f753b4abeae5b77012.js', $publicPath);
        $cachedFilePath = sprintf('%s/ru-13df9bdf8e0c78f753b4abeae5b77012.js', $this->cacheDir);
        $this->assertFileExists($cachedFilePath);
        $this->assertEquals($templateContent, file_get_contents($cachedFilePath));
        $this->assertTrue(unlink($cachedFilePath));
    }
}
