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

/**
 * The Mermaid keyword is "break"; the class is named BreakBlock because "Break"
 * is a reserved word in PHP and cannot be used as a class name.
 */
final class BreakBlock extends Block
{
    public function __construct(string $label)
    {
        parent::__construct('break', $label);
    }
}
