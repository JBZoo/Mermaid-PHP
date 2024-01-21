<?php

/**
 * JBZoo Toolbox - Mermaid-PHP.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Mermaid-PHP
 */

declare(strict_types=1);

namespace JBZoo\MermaidPHP\ClassDiagram\ConceptNamespace;

use JBZoo\MermaidPHP\ClassDiagram\Concept\Concept;

class ConceptNamespace
{
    public function __construct(
        protected string $identifier,
        /** @var Concept[] */
        protected array $classes,
    ) {
    }

    public function __toString(): string
    {
        $spaces = \str_repeat(' ', 4);

        $result = [];

        $result[] = \sprintf('namespace %s {', $this->identifier);

        /** @var Concept $class */
        foreach ($this->classes as $class) {
            foreach ($class->getLinesToRender() as $line) {
                $result[] = $spaces . $line;
            }
        }

        $result[] = '}';

        return \implode(\PHP_EOL, $result);
    }
}
