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

enum RelationType
{
    case INHERITANCE;
    case COMPOSITION;
    case AGGREGATION;
    case ASSOCIATION;
    case DEPENDENCY;
    case REALIZATION;
    // not documented
    case LOLLILOP;

    public function getDefaultLink(): Link
    {
        return match ($this) {
            self::DEPENDENCY, self::REALIZATION => Link::DASHED,
            default => Link::SOLID,
        };
    }

    /**
     * @psalm-suppress PossiblyNullOperand
     */
    public function renderRelation(Link $link, ?RelationType $inverse): string
    {
        return match ($this) {
            self::AGGREGATION, self::COMPOSITION => $this->renderDirect() . $link->value . $inverse?->renderInverse(),
            default => $inverse?->renderInverse() . $link->value . $this->renderDirect(),
        };
    }

    private function renderDirect(): string
    {
        return match ($this) {
            self::INHERITANCE, self::REALIZATION => '|>',
            self::COMPOSITION => '*',
            self::AGGREGATION => 'o',
            self::ASSOCIATION, self::DEPENDENCY => '>',
            self::LOLLILOP => '(),'
        };
    }

    private function renderInverse(): string
    {
        return match ($this) {
            self::INHERITANCE, self::REALIZATION => '<|',
            self::ASSOCIATION, self::DEPENDENCY => '<',
            default => $this->renderDirect(),
        };
    }
}
