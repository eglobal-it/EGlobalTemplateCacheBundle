<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Command;

use EGlobal\Bundle\TemplateCacheBundle\Dumper\TemplateFileDumperInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumpTemplateCacheCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $name = 'eglobal:template-cache:dump';

        $this
            ->setName($name)
            ->setDescription('Dump templates into public cache.')

            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run.')

            ->setHelp(<<<HELP
Dump all cacheable templates:
<info>$ ./bin/console $name</info>
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Dumping cacheable templates');

        $dumper = $this->getDumper($input->getOption('dry-run'));
        $dumper->setLogger(new ConsoleLogger($output));
        $dumper->dump();
    }

    protected function getDumper(bool $debug = false): TemplateFileDumperInterface
    {
        return ($debug)
            ? $this->getContainer()->get('eglobal_template_cache.dumper.debug_template_file_dumper')
            : $this->getContainer()->get('eglobal_template_cache.dumper.template_file_dumper');
    }
}
