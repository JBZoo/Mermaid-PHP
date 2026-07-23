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
    public const string TOP_BOTTOM = 'TB';
    public const string BOTTOM_TOP = 'BT';
    public const string LEFT_RIGHT = 'LR';
    public const string RIGHT_LEFT = 'RL';

    private const int RENDER_SHIFT = 4;

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

    public function render(bool $isMainGraph = true, int $shift = 0): string
    {
        $spaces    = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        if ($isMainGraph) {
            $result = ["graph {$this->params['direction']};"];
        } else {
            $result = ["{$spaces}subgraph " . Helper::escape((string)$this->params['title'])];
        }

        $result = \array_merge($result, $this->renderItems($this->nodes, $spacesSub, $isMainGraph));
        $result = \array_merge($result, $this->renderItems($this->links, $spacesSub, $isMainGraph));

        foreach ($this->subGraphs as $subGraph) {
            $result[] = $subGraph->render(false, $shift + 4);
        }

        if ($isMainGraph) {
            foreach ($this->styles as $style) {
                $result[] = $spaces . $style . ';';
            }

            foreach ($this->renderInteractions() as $line) {
                $result[] = $spaces . $line;
            }
        } else {
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
        ?string $css = null,
    ): self {
        $source = $this->getNode($sourceNodeId);
        if ($source === null) {
            throw new Exception("Source node id=\"{$sourceNodeId}\" not found");
        }

        $target = $this->getNode($targetNodeId);
        if ($target === null) {
            throw new Exception("Target node id=\"{$targetNodeId}\" not found");
        }

        return $this->addLink(new Link($source, $target, $text, $style, $css));
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

    /**
     * Render a block of nodes or links, applying the optional abc-order sort.
     *
     * @param array<Link|Node> $items
     *
     * @return string[]
     */
    private function renderItems(array $items, string $spacesSub, bool $isMainGraph): array
    {
        if (\count($items) === 0) {
            return [];
        }

        $lines = [];

        foreach ($items as $item) {
            $lines[] = $spacesSub . $item->__toString();
        }

        if ($this->params['abc_order'] === true) {
            \sort($lines);
        }

        if ($isMainGraph) {
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * Global click/linkStyle directives for the whole graph tree.
     *
     * @return string[]
     */
    private function renderInteractions(): array
    {
        $lines = [];

        foreach ($this->flattenLinks() as $index => $link) {
            $css = $link->getCss();
            if ($css !== null) {
                $lines[] = "linkStyle {$index} {$css};";
            }
        }

        foreach ($this->flattenNodes() as $node) {
            $clickStatement = $node->getClickStatement();
            if ($clickStatement !== null) {
                $lines[] = $clickStatement;
            }
        }

        return $lines;
    }

    /**
     * Links across the whole graph tree, in render order (own links first, then
     * sub-graphs depth-first). Mirrors render()'s per-graph ordering incl. abc_order,
     * so a link's array position equals its 0-based Mermaid linkStyle index.
     *
     * @return Link[]
     */
    private function flattenLinks(): array
    {
        $links = $this->links;
        if ($this->params['abc_order'] === true) {
            \usort($links, static fn (Link $linkA, Link $linkB): int => (string)$linkA <=> (string)$linkB);
        }

        foreach ($this->subGraphs as $subGraph) {
            $links = \array_merge($links, $subGraph->flattenLinks());
        }

        return $links;
    }

    /**
     * Nodes across the whole graph tree, in render order.
     *
     * @return Node[]
     */
    private function flattenNodes(): array
    {
        $nodes = \array_values($this->nodes);

        foreach ($this->subGraphs as $subGraph) {
            $nodes = \array_merge($nodes, $subGraph->flattenNodes());
        }

        return $nodes;
    }
}
