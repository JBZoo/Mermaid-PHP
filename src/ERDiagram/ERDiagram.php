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

namespace JBZoo\MermaidPHP\ERDiagram;

use JBZoo\MermaidPHP\ERDiagram\Relation\Relation;
use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Render;

class ERDiagram
{
    private const RENDER_SHIFT = 4;

    /** @var Entity[] */
    protected array $entities = [];

    /** @var Relation[] */
    protected array $relations = [];

    /** @var mixed[] */
    protected array $params = [
        'abc_order' => false,
        'title'     => '',
    ];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function addEntity(Entity $entity): self
    {
        $this->entities[$entity->getId()] = $entity;

        return $this;
    }

    public function addRelation(Relation $relation): self
    {
        $this->relations[$relation->getId()] = $relation;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(int $shift = 0): string
    {
        $spaces    = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        $result = [];
        $params = $this->getParams();

        if (!empty($params['title'])) {
            $result[] = sprintf('---%s---', \PHP_EOL . "title: " . $params['title'] . \PHP_EOL);
        }

        $result[] = "erDiagram";

        if (\count($this->relations) > 0) {
            $tmp = [];

            foreach ($this->relations as $relation) {
                $tmp[] = $spacesSub . $relation;
            }

            if ($this->params['abc_order'] === true) {
                \sort($tmp);
            }

            $result = \array_merge($result, $tmp);
        }

        $entitiesWithProps = array_filter($this->entities, function(Entity $entity) {
            return !empty($entity->getProps());
        });

        if (\count($entitiesWithProps) > 0) {
            $tmp = [];

            foreach ($entitiesWithProps as $entity) {
                $classEntity = $entity->renderProps();
                if ($classEntity) {
                    $tmp[] = $spacesSub . $classEntity;
                }
            }

            if ($this->params['abc_order'] === true) {
                \sort($tmp);
            }

            $result = \array_merge($result, $tmp);
        }

        $result[] = '';
        return \implode(\PHP_EOL, $result);
    }

    /**
     * @param array<string> $params
     */
    public function renderHtml(array $params = []): string
    {
        return Render::html($this, $params);
    }

    public function getLiveEditorUrl(): string
    {
        return Helper::getLiveEditorUrl($this);
    }

    public function __toString(): string
    {
        return $this->render();
    }

}
