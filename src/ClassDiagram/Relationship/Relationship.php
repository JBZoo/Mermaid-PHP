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
        private Concept $classA,
        private Concept $classB,
        private RelationType $relationType,
        private ?string $label = null,
        private Cardinality|string|null $cardinalityA = null,
        private Cardinality|string|null $cardinalityB = null,
        private ?Link $link = null,
        private ?RelationType $inverseRelationType = null,
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
        if ($this->link === null) {
            return '';
        }

        return $this->relationType->renderRelation($this->link, $this->inverseRelationType);
    }

    private static function renderCardinality(Cardinality|string|null $cardinality): string
    {
        if ($cardinality === null) {
            return '';
        }

        $value = $cardinality instanceof Cardinality ? $cardinality->value : $cardinality;

        return \sprintf(' "%s"', $value);
    }
}
