<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Dumper;

use EGlobal\Bundle\TemplateCacheBundle\Dumper\DebugTemplateFileDumper;

class DebugTemplateFileDumperTest extends TemplateFileDumperTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dumper = new DebugTemplateFileDumper($this->templateFinder, $this->templateCache, $this->locales);
        $this->dumper->setLogger($this->logger);
    }

    /**
     * @test
     */
    public function it_dumps_templates()
    {
        $this->templateCache->expects($this->never())->method('writeTemplate');
        $this->templateCache->expects($this->never())->method('writeMap');

        $foundTemplates = $this->generateFoundTemplates(3);

        $methodIteratorIndex = 0;
        foreach ($this->locales as $locale) {
            $templateMap = [];

            foreach ($foundTemplates as $templateIndex => $template) {
                $templateMap[sprintf('test.foo.route.%d', $templateIndex + 1)] = sprintf('/public/foo/%s/%d.html', $locale, $templateIndex + 1);

                $this->logger
                    ->expects($this->at($methodIteratorIndex))
                    ->method('info')
                    ->with(sprintf(' - %s: Processing Foo\\Bar\\Test::template%dAction', $locale, $templateIndex + 1));

                ++$methodIteratorIndex;
            }

            $this->assertEquals(3, count($templateMap));

            $this->logger
                ->expects($this->at($methodIteratorIndex))
                ->method('info')
                ->with(sprintf(' [%s] 3 templates will be added to map.', strtoupper($locale)));

            ++$methodIteratorIndex;
        }

        $this->dumper->dump();
    }
}
