<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Model;

class CacheableTemplate
{
    private $methodName;
    private $routeName;
    private $template;
    private $templateVars;

    public function __construct(string $methodName, string $routeName, string $template, array $templateVars = [])
    {
        $this->methodName = $methodName;
        $this->routeName = $routeName;
        $this->template = $template;
        $this->templateVars = $templateVars;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }
}
