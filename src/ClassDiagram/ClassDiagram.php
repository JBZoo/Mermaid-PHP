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

namespace JBZoo\MermaidPHP\ClassDiagram;

use JBZoo\MermaidPHP\ClassDiagram\Concept\Concept;
use JBZoo\MermaidPHP\ClassDiagram\ConceptNamespace\ConceptNamespace;
use JBZoo\MermaidPHP\ClassDiagram\Relationship\Relationship;
use JBZoo\MermaidPHP\Direction;
use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Render;

final class ClassDiagram
{
    private ?string $title        = null;
    private ?Direction $direction = null;

    /** @var ConceptNamespace[] */
    private array $namespaces = [];

    /** @var Concept[] */
    private array $classes = [];

    /** @var Relationship[] */
    private array $relationships = [];

    public function __toString(): string
    {
        return $this->render();
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDirection(Direction $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function addClass(Concept $class): self
    {
        $this->classes[$class->getId()] = $class;

        return $this;
    }

    public function addNamespace(ConceptNamespace $namespace): self
    {
        $this->namespaces[] = $namespace;

        return $this;
    }

    public function addRelationship(Relationship $relationship): self
    {
        $this->relationships[] = $relationship;

        return $this;
    }

    public function render(): string
    {
        $result = [];

        if ($this->title !== null) {
            $result[] = \sprintf('---%s---', \PHP_EOL . 'title: ' . $this->title . \PHP_EOL);
        }

        $result[] = 'classDiagram';

        if ($this->direction !== null) {
            $result[] = \sprintf('direction %s', $this->direction->value);
        }

        foreach ($this->namespaces as $namespace) {
            $result[] = $namespace;
        }

        foreach ($this->classes as $class) {
            $result[] = $class;
        }

        foreach ($this->relationships as $relationship) {
            $result[] = $relationship;
        }

        $result[] = '';

        return \implode(\PHP_EOL, $result);
    }

    /**
     * @suppress PhanPossiblyUndeclaredProperty
     */
    public function getParams(): array
    {
        return \array_filter([
            'title'     => $this->title,
            'direction' => $this->direction?->value,
        ], static fn ($value) => $value !== null);
    }

    /**
     * @param  array<string>  $params
     * @throws \JsonException
     */
    public function renderHtml(array $params = []): string
    {
        return Render::html($this, $params);
    }

    public function getLiveEditorUrl(): string
    {
        return Helper::getLiveEditorUrl($this);
    }
}
