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

namespace JBZoo\MermaidPHP\SequenceDiagram\Block;

final class Alt extends Block
{
    public function __construct(string $label)
    {
        parent::__construct('alt', $label);
    }

    public function addElse(string $label = ''): static
    {
        return $this->addSection('else', $label === '' ? null : $label);
    }
}
