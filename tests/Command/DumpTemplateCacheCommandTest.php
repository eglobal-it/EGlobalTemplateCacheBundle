<?php

declare(strict_types=1);

namespace Tests\EGlobal\Bundle\TemplateCacheBundle\Command;

use EGlobal\Bundle\TemplateCacheBundle\Command\DumpTemplateCacheCommand;
use EGlobal\Bundle\TemplateCacheBundle\Dumper\DebugTemplateFileDumper;
use EGlobal\Bundle\TemplateCacheBundle\Dumper\TemplateFileDumper;
use EGlobal\Bundle\TemplateCacheBundle\Dumper\TemplateFileDumperInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DumpTemplateCacheCommandTest extends KernelTestCase
{
    /**
     * @var DumpTemplateCacheCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    private $command;

    /**
     * @var TemplateFileDumperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dumper;

    /**
     * @var TemplateFileDumperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $debugDumper;

    /**
     * @var CommandTester
     */
    private $tester;

    public function setUp()
    {
        static::bootKernel();

        $this->dumper = $this->getMockBuilder(TemplateFileDumper::class)
            ->disableOriginalConstructor()
            ->setMethods(['dump'])
            ->getMock();

        $this->debugDumper = $this->getMockBuilder(DebugTemplateFileDumper::class)
            ->disableOriginalConstructor()
            ->setMethods(['dump'])
            ->getMock();

        $this->command = $this->getMockBuilder(DumpTemplateCacheCommand::class)
            ->setMethods(['getDumper'])
            ->getMock();

        $application = new Application(self::$kernel);
        $application->add($this->command);

        $this->tester = new CommandTester($this->command);
    }

    /**
     * @test
     */
    public function it_executes_command()
    {
        $this->command->expects($this->once())->method('getDumper')->with(false)->willReturn($this->dumper);
        $this->dumper->expects($this->once())->method('dump');
        $this->debugDumper->expects($this->never())->method('dump');

        $lines = $this->execute()->getOutputLines();

        // Assert header.
        $this->assertEquals('Dumping cacheable templates', $lines[0]);
        $this->assertStringStartsWith('=====', $lines[1]);
    }

    /**
     * @test
     */
    public function it_executes_command_dry_run()
    {
        $this->command->expects($this->once())->method('getDumper')->with(true)->willReturn($this->debugDumper);
        $this->dumper->expects($this->never())->method('dump');
        $this->debugDumper->expects($this->once())->method('dump');

        $lines = $this->execute(['--dry-run' => true])->getOutputLines();

        // Assert header.
        $this->assertEquals('Dumping cacheable templates', $lines[0]);
        $this->assertStringStartsWith('=====', $lines[1]);
    }

    private function getOutputLines(): array
    {
        return explode("\n", trim($this->tester->getDisplay(true)));
    }

    private function execute(array $input = []): self
    {
        $this->tester->execute(array_merge($input, ['command' => $this->command->getName()]));

        return $this;
    }

    protected static function getKernelClass(): string
    {
        return 'Tests\EGlobal\Bundle\TemplateCacheBundle\Fixtures\AppKernel';
    }
}
