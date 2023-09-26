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

namespace JBZoo\MermaidPHP\ERDiagram\Relation;

class OneToOne extends Relation
{
    public function getLink(): string
    {
        if ($this->cardinality === self::ZERO_OR_ONE) {
            return \sprintf('%s%s%s', '||', $this->getIdentification(), 'o|');
        }

        return \sprintf('%s%s%s', '||', $this->getIdentification(), '||');
    }
}
