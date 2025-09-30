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

namespace JBZoo\MermaidPHP\ClassDiagram\Concept;

use JBZoo\MermaidPHP\Exception;

class Method implements \Stringable
{
    public function __construct(
        private string $name,
        /** @var Argument[] */
        private array $arguments = [],
        private ?string $returnType = null,
        private ?Visibility $visibility = null,
        private bool $isAbstract = false,
        private bool $isStatic = false,
    ) {
        if ($this->isAbstract && $this->isStatic) {
            throw new Exception('A method could not be both abstract and static');
        }
    }

    public function __toString(): string
    {
        $output = [];

        $output[] = \sprintf('%s(%s)', $this->name, \implode(',', $this->arguments));

        if ($this->returnType !== null) {
            $output[] = $this->returnType;
        }

        return $this->renderVisibility() . \implode(' ', $output) . $this->renderClassifier();
    }

    private function renderVisibility(): string
    {
        if ($this->visibility === null) {
            return '';
        }

        return $this->visibility->value;
    }

    private function renderClassifier(): string
    {
        if ($this->isStatic) {
            return '$';
        }

        if ($this->isAbstract) {
            return '*';
        }

        return '';
    }
}
