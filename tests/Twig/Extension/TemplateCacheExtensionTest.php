<?php

declare(strict_types=1);

namespace Tests\EGlobal\Bundle\TemplateCacheBundle\Twig\Extension;

use EGlobal\Bundle\TemplateCacheBundle\Twig\Extension\TemplateCacheExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class TemplateCacheExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->createMapFiles();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unlink(sys_get_temp_dir() . '/templates/ru-123.js');
        unlink(sys_get_temp_dir() . '/templates/ru-456.js');
    }

    /**
     * @test
     */
    public function it_returns_latest_map_file_name()
    {
        $this->assertEquals('ru-456.js', $this->loadTemplate('{{ jsTemplateMapFileName(\'ru\') }}')->render([]));
    }

    /**
     * @test
     */
    public function it_returns_blank_string_when_no_map_file_exists()
    {
        $this->assertEmpty($this->loadTemplate('{{ jsTemplateMapFileName(\'en\') }}')->render([]));
    }

    private function loadTemplate($template): TemplateWrapper
    {
        $twig = new Environment(new ArrayLoader(['index' => $template]));
        $twig->addExtension(new TemplateCacheExtension(sys_get_temp_dir() . '/templates'));

        return $twig->load('index');
    }

    protected function createMapFiles()
    {
        @mkdir(sys_get_temp_dir() . '/templates', 0777, true);
        file_put_contents(sys_get_temp_dir() . '/templates/ru-123.js', <<<'JS'
alert('Previous file');
JS
        );
        touch(sys_get_temp_dir() . '/templates/ru-123.js', time() - 1000);

        file_put_contents(sys_get_temp_dir() . '/templates/ru-456.js', <<<'JS'
alert('Current file');
JS
        );
    }
}
