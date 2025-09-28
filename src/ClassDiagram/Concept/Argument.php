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

final class Argument implements \Stringable
{
    public function __construct(
        private string $name,
        private ?string $type = null,
    ) {
    }

    public function __toString(): string
    {
        $output = [];

        if ($this->type !== null) {
            $output[] = $this->type;
        }

        $output[] = $this->name;

        return \implode(' ', $output);
    }
}
