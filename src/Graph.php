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

namespace JBZoo\MermaidPHP;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Graph
{
    public const TOP_BOTTOM = 'TB';
    public const BOTTOM_TOP = 'BT';
    public const LEFT_RIGHT = 'LR';
    public const RIGHT_LEFT = 'RL';

    private const RENDER_SHIFT = 4;

    /** @var Graph[] */
    private array $subGraphs = [];

    /** @var Node[] */
    private array $nodes = [];

    /** @var Link[] */
    private array $links = [];

    /** @var mixed[] */
    private array $params = [
        'abc_order' => false,
        'title'     => 'Graph',
        'direction' => self::TOP_BOTTOM,
    ];

    /** @var string[] */
    private $styles = [];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(bool $isMainGraph = true, int $shift = 0): string
    {
        $spaces    = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        if ($isMainGraph) {
            $result = ["graph {$this->params['direction']};"];
        } else {
            $result = ["{$spaces}subgraph " . Helper::escape((string)$this->params['title'])];
        }

        if (\count($this->nodes) > 0) {
            $tmp = [];

            foreach ($this->nodes as $node) {
                $tmp[] = $spacesSub . $node->__toString();
            }

            if ($this->params['abc_order'] === true) {
                \sort($tmp);
            }

            $result = \array_merge($result, $tmp);
            if ($isMainGraph) {
                $result[] = '';
            }
        }

        if (\count($this->links) > 0) {
            $tmp = [];

            foreach ($this->links as $link) {
                $tmp[] = $spacesSub . $link->__toString();
            }

            if ($this->params['abc_order'] === true) {
                \sort($tmp);
            }

            $result = \array_merge($result, $tmp);
            if ($isMainGraph) {
                $result[] = '';
            }
        }

        foreach ($this->subGraphs as $subGraph) {
            $result[] = $subGraph->render(false, $shift + 4);
        }

        if ($isMainGraph && \count($this->styles) > 0) {
            foreach ($this->styles as $style) {
                $result[] = $spaces . $style . ';';
            }
        }

        if (!$isMainGraph) {
            $result[] = "{$spaces}end";
        }

        return \implode(\PHP_EOL, $result);
    }

    public function addNode(Node $node): self
    {
        $this->nodes[$node->getId()] = $node;

        return $this;
    }

    public function addLink(Link $link): self
    {
        $this->links[] = $link;

        return $this;
    }

    public function addLinkByIds(
        string $sourceNodeId,
        string $targetNodeId,
        string $text = '',
        int $style = Link::ARROW,
    ): self {
        $source = $this->getNode($sourceNodeId);
        if ($source === null) {
            throw new Exception("Source node id=\"{$sourceNodeId}\" not found");
        }

        $target = $this->getNode($targetNodeId);
        if ($target === null) {
            throw new Exception("Target node id=\"{$targetNodeId}\" not found");
        }

        return $this->addLink(new Link($source, $target, $text, $style));
    }

    public function addStyle(string $style): self
    {
        $this->styles[] = $style;

        return $this;
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

    public function setParams(array $params): self
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function addSubGraph(self $subGraph): self
    {
        $this->subGraphs[] = $subGraph;

        return $this;
    }

    public function getNode(string $nodeId): ?Node
    {
        if (Node::isSafeMode()) {
            $nodeId = Helper::getId($nodeId);
        }

        return $this->nodes[$nodeId] ?? null;
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
