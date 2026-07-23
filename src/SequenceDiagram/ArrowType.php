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

namespace JBZoo\MermaidPHP\SequenceDiagram;

enum ArrowType: string
{
    case SOLID        = '->';
    case DOTTED       = '-->';
    case SOLID_ARROW  = '->>';
    case DOTTED_ARROW = '-->>';
    case BI_SOLID     = '<<->>';
    case BI_DOTTED    = '<<-->>';
    case CROSS        = '-x';
    case DOTTED_CROSS = '--x';
    case ASYNC        = '-)';
    case DOTTED_ASYNC = '--)';
}
