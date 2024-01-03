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

use JBZoo\MermaidPHP\ClassDiagram\Concept\Concept;

class Relationship
{
    public function __construct(
        protected Concept $classA,
        protected Concept $classB,
        protected RelationType $relationType,
        protected ?string $label = null,
        protected null|Cardinality|string $cardinalityA = null,
        protected null|Cardinality|string $cardinalityB = null,
        protected ?Link $link = null,
        protected ?RelationType $inverseRelationType = null,
    ) {
        if ($this->link === null) {
            $this->link = $this->relationType->getDefaultLink();
        }
    }

    public function __toString(): string
    {
        $result = \sprintf(
            '%s%s %s%s %s',
            $this->classA->getId(),
            self::renderCardinality($this->cardinalityA),
            $this->renderRelation(),
            self::renderCardinality($this->cardinalityB),
            $this->classB->getId(),
        );

        if ($this->label !== null) {
            $result .= \sprintf(' : %s', $this->label);
        }

        return $result;
    }

    private function renderRelation(): string
    {
        return $this->relationType->renderRelation($this->link, $this->inverseRelationType);
    }

    private static function renderCardinality(null|Cardinality|string $cardinality): string
    {
        if ($cardinality === null) {
            return '';
        }

        $value = $cardinality instanceof Cardinality ? $cardinality->value : $cardinality;

        return \sprintf(' "%s"', $value);
    }
}
