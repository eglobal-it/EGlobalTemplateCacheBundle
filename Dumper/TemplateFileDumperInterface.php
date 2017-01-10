<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Dumper;

use Psr\Log\LoggerInterface;

interface TemplateFileDumperInterface
{
    public function dump();

    public function setLogger(LoggerInterface $logger);
}
