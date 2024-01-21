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

namespace JBZoo\MermaidPHP\ClassDiagram\Relationship;

enum Cardinality: string
{
    case ONE         = '1';
    case ZERO_OR_ONE = '0..1';
    case ONE_OR_MORE = '1..*';
    case MANY        = '*';
    case N           = 'n';
    case ZERO_TO_N   = '0..n';
    case ONE_TO_N    = '1..n';
}
