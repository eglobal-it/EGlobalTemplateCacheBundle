<?php

declare(strict_types=1);

namespace EGlobal\Bundle\TemplateCacheBundle\Helper;

class FileContentTokenizer
{
    /**
     * @return string|null
     */
    public static function getFullyQualifiedClassName(string $fileContent)
    {
        $fullyQualifiedClassName = [];

        if (null !== $namespace = self::getNamespace($fileContent)) {
            array_push($fullyQualifiedClassName, $namespace);
        }

        if (null !== $className = self::getClassName($fileContent)) {
            array_push($fullyQualifiedClassName, $className);
        }

        return (empty($fullyQualifiedClassName)) ? null : implode('\\', $fullyQualifiedClassName);
    }

    /**
     * @return string|null
     */
    public static function getNamespace(string $fileContent)
    {
        $namespace = [];
        $namespaceStarted = false;

        foreach (token_get_all($fileContent) as $token) {
            if (is_array($token)) {
                if (T_NAMESPACE === $token[0]) {
                    $namespaceStarted = true;
                } elseif ($namespaceStarted && T_STRING === $token[0]) {
                    array_push($namespace, $token[1]);
                }
            } elseif (count($namespace) && (';' === $token)) {
                return implode('\\', $namespace);
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public static function getClassName(string $fileContent)
    {
        $classNameStarted = false;

        foreach (token_get_all($fileContent) as $token) {
            if (!is_array($token)) {
                continue;
            }

            if (T_CLASS === $token[0]) {
                $classNameStarted = true;
            } elseif ($classNameStarted && T_STRING === $token[0]) {
                return $token[1];
            }
        }

        return null;
    }
}
