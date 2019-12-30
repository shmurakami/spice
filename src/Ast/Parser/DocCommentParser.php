<?php

namespace shmurakami\Spice\Ast\Parser;

trait DocCommentParser
{
    /**
     * @param string $namespace
     * @param string $docComment
     * @param string $annotationName prefixed by '@'
     * @return string[]
     */
    private function parseDocComment(string $namespace, string $docComment, string $annotationName)
    {
        $classTypeLine = '';

        foreach (explode("\n", $docComment) as $commentLine) {
            // space is needed to declaration annotation
            if (strpos($commentLine, "$annotationName ") !== false) {
                $classTypeLine = $commentLine;
                break;
            }
        }

        if (!$classTypeLine) {
            return [];
        }

        $dependentClassFqcnList = [];

        // should be this? ^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$
        // https://www.php.net/manual/en/language.variables.basics.php
        preg_match("/$annotationName (.+)/", $commentLine, $matches);
        // can be multiple
        $classFqcnList = explode('|', $matches[1]);

        for ($i = 0, $count = count($classFqcnList); $i < $count; $i++) {
            $classFqcn = trim($classFqcnList[$i]);
            // end parts may have additional comment
            if ($i === $count - 1) {
                $parts = explode(' ', $classFqcn);
                // FQCN has \\ prefix in doc comment but it's not needed
                // trim space and backslash
                $classFqcn = trim($parts[0], " \t\n\r \v\\");
            }

            if ($this->isNotSupportedPhpBaseType($classFqcn)) {
                continue;
            }

            // if \ is included it means fqcn. no need to touch
            if (strpos($classFqcn, '\\') === false) {
                // global space or same namespace instance
                // if class has namespace, assume as same namespace
                // otherwise assume as global namespace
                $baseNamespace = $namespace ?? '';
                $classFqcn = $baseNamespace . '\\' . $classFqcn;
            }

            $dependentClassFqcnList[] = $classFqcn;
        }
        return $dependentClassFqcnList;
    }

    private function isNotSupportedPhpBaseType(string $classType): bool
    {
        return in_array($classType, [
            'int', 'integer',
            'string',
            'bool', 'boolean',
            'float',
            'double',
            'object',
            'array', // array can be callable...
            'callable',
            'iterable',
            'mixed',
            'number',
            'void',
        ], true);
    }
}
