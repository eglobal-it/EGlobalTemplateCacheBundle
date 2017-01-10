<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Finder;

use Doctrine\Common\Annotations\AnnotationReader;
use EGlobal\Bundle\TemplateCacheBundle\Finder\CacheableTemplateFinder;
use EGlobal\Bundle\TemplateCacheBundle\Model\CacheableTemplate;
use Symfony\Component\Config\FileLocatorInterface;

class CacheableTemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileLocator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->populateFiles();

        $this->fileLocator = $this->getMock(FileLocatorInterface::class);
        $this->fileLocator->expects($this->at(0))->method('locate')->with('@EGlobalTemplateCache/Controller')->willReturn(sys_get_temp_dir() . '/EGlobal\Bundle\TemplateCacheBundle/Controller');
        $this->fileLocator->expects($this->at(1))->method('locate')->with('%kernel.root_dir%/../OtherTestBundle/Controller')->willReturn(sys_get_temp_dir() . '/OtherTestBundle/Controller');
    }

    /**
     * @test
     */
    public function it_finds_templates()
    {
        $finder = new CacheableTemplateFinder(
            new AnnotationReader(),
            $this->fileLocator,
            ['@EGlobalTemplateCache/Controller', '%kernel.root_dir%/../OtherTestBundle/Controller'],
            false
        );

        $result = [];
        foreach ($finder->find() as $cacheableTemplate) {
            /* @var $cacheableTemplate CacheableTemplate */
            $this->assertInstanceOf(CacheableTemplate::class, $cacheableTemplate);
            $result[$cacheableTemplate->getRouteName()] = $cacheableTemplate->getTemplate();
        }

        $this->assertEquals(4, count($result));
        $this->assertEquals([], array_diff($result, [
            'template_cache.foo.index' => '@EGlobalTemplateCache:Foo:index.html.twig',
            'template_cache.foo.list' => '@EGlobalTemplateCache:Foo:list.html.twig',
            'template_cache.bar.index' => '@EGlobalTemplateCache:Foo/Bar:index.html.twig',
            'template_cache.bar.list' => '@EGlobalTemplateCache:Foo/Bar:list.html.twig',
        ]));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not resolve route name of EGlobal\Bundle\TemplateCacheBundle\Controller\FooController::listAction. Please, check if it has an exposed route.
     */
    public function it_throws_error_on_no_exposed_route()
    {
        (new CacheableTemplateFinder(
            new AnnotationReader(),
            $this->fileLocator,
            ['@EGlobalTemplateCache/Controller', '%kernel.root_dir%/../OtherTestBundle/Controller'],
            true
        ))->find();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unlink(sys_get_temp_dir() . '/EGlobal\Bundle\TemplateCacheBundle/Controller/FooController.php');
        unlink(sys_get_temp_dir() . '/OtherTestBundle/Controller/Bar/BarController.php');
    }

    protected function populateFiles()
    {
        @mkdir(sys_get_temp_dir() . '/EGlobal\Bundle\TemplateCacheBundle/Controller', 0777, true);
        file_put_contents(sys_get_temp_dir() . '/EGlobal\Bundle\TemplateCacheBundle/Controller/FooController.php', <<<'PHP'
<?php

namespace EGlobal\Bundle\TemplateCacheBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EGlobal\Bundle\TemplateCacheBundle\Annotation\CacheableTemplate;

class FooController {
    
    /**
     * @Route("/foo/index", name="template_cache.foo.index", options={"expose"=true})
     * @CacheableTemplate("@EGlobalTemplateCache:Foo:index.html.twig")
     */
    public function indexAction()
    {
    }
    
    /**
     * @Route("/foo/list", name="template_cache.foo.list")
     * @CacheableTemplate("@EGlobalTemplateCache:Foo:list.html.twig")
     */
    public function listAction()
    {
    }
    
    /**
     * @Route("/foo/edit", name="template_cache.foo.edit")
     * @Template("@EGlobalTemplateCache:Foo:edit.html.twig")
     */
    public function editAction()
    {
    }
}
PHP
        );

        include_once sys_get_temp_dir() . '/EGlobal\Bundle\TemplateCacheBundle/Controller/FooController.php';

        @mkdir(sys_get_temp_dir() . '/OtherTestBundle/Controller/Bar', 0777, true);
        file_put_contents(sys_get_temp_dir() . '/OtherTestBundle/Controller/Bar/BarController.php', <<<'PHP'
<?php

namespace EGlobal\Bundle\TemplateCacheBundle\Controller\Bar;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EGlobal\Bundle\TemplateCacheBundle\Annotation\CacheableTemplate;

class BarController {
    
    /**
     * @Route("foo/bar-index")
     * @Route("/foo/bar/index", name="template_cache.bar.index", options={"expose"=true})
     * @CacheableTemplate("@EGlobalTemplateCache:Foo/Bar:index.html.twig")
     */
    public function indexAction()
    {
    }
    
    /**
     * @Route("/foo/bar/list", name="template_cache.bar.list", options={"expose"=true})
     * @CacheableTemplate("@EGlobalTemplateCache:Foo/Bar:list.html.twig")
     */
    public function listAction()
    {
    }
    
    /**
     * @Route("/foo/bar/edit", name="template_cache.bar.edit")
     * @Template("@EGlobalTemplateCache:Foo/Bar:edit.html.twig")
     */
    public function editAction()
    {
    }
}
PHP
        );

        include_once sys_get_temp_dir() . '/OtherTestBundle/Controller/Bar/BarController.php';
    }
}
