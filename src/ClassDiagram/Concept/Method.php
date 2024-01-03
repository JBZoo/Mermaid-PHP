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
        protected string $name,
        /** @var Argument[] */
        protected array $arguments = [],
        protected ?string $returnType = null,
        protected ?Visibility $visibility = null,
        protected bool $isAbstract = false,
        protected bool $isStatic = false,
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
