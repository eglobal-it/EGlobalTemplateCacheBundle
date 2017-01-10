<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Tests\Helper;

use EGlobal\Bundle\TemplateCacheBundle\Helper\FileContentTokenizer;

class FileContentTokenizerTest extends \PHPUnit_Framework_TestCase
{
    private $content = <<<PHP
<?php

declare(strict_types = 1);

namespace Foo\Bar\Test;

class TestClass {
    
    public function myMethod() {}
    
}

PHP;

    /**
     * @test
     */
    public function it_resolves_namespace_from_file_content()
    {
        $this->assertEquals('Foo\Bar\Test', FileContentTokenizer::getNamespace($this->content));
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_namespace_found()
    {
        $content = <<<'PHP'
<?php

declare(strict_types = 1);

class TestClass {
    
    public function myMethod() {}
    
}
PHP;

        $this->assertNull(FileContentTokenizer::getNamespace($content));
    }

    /**
     * @test
     */
    public function it_resolves_class_name_from_file_content()
    {
        $this->assertEquals('TestClass', FileContentTokenizer::getClassName($this->content));
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_class_found()
    {
        $content = <<<'PHP'
<?php

function myFunction() {
    echo 'test';
}
PHP;

        $this->assertNull(FileContentTokenizer::getClassName($content));
    }

    /**
     * @test
     */
    public function it_resolves_fully_qualified_class_name_from_file_content()
    {
        $this->assertEquals('Foo\Bar\Test\TestClass', FileContentTokenizer::getFullyQualifiedClassName($this->content));
    }

    /**
     * @test
     */
    public function it_returns_null_when_neither_namespace_nor_class_found()
    {
        $content = <<<'PHP'
<?php

function myFunction() {
    echo 'test';
}
PHP;

        $this->assertNull(FileContentTokenizer::getFullyQualifiedClassName($content));
    }
}
