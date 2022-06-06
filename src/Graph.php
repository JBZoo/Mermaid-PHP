<?php

/**
 * JBZoo Toolbox - Mermaid-PHP
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Mermaid-PHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Mermaid-PHP
 */

declare(strict_types=1);

namespace JBZoo\MermaidPHP;

/**
 * Class Graph
 * @package JBZoo\MermaidPHP
 */
class Graph
{
    public const TOP_BOTTOM = 'TB';
    public const BOTTOM_TOP = 'BT';
    public const LEFT_RIGHT = 'LR';
    public const RIGHT_LEFT = 'RL';

    private const RENDER_SHIFT = 4;

    /**
     * @var Graph[]
     */
    protected array $subGraphs = [];

    /**
     * @var Node[]
     */
    protected array $nodes = [];

    /**
     * @var Link[]
     */
    protected array $links = [];

    /**
     * @var mixed[]
     */
    protected array $params = [
        'abc_order' => false,
        'title'     => 'Graph',
        'direction' => self::TOP_BOTTOM,
    ];

    /**
     * @var String[]
     */
    protected $styles = [];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param bool $isMainGraph
     * @param int  $shift
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(bool $isMainGraph = true, int $shift = 0): string
    {
        $spaces = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        if ($isMainGraph) {
            $result = ["graph {$this->params['direction']};"];
        } else {
            $result = ["{$spaces}subgraph " . Helper::escape((string)$this->params['title'])];
        }

        if (\count($this->nodes) > 0) {
            $tmp = [];
            foreach ($this->nodes as $node) {
                $tmp[] = $spacesSub . $node;
            }
            if ($this->params['abc_order']) {
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
                $tmp[] = $spacesSub . $link;
            }
            if ($this->params['abc_order']) {
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

    /**
     * @param Node $node
     * @return $this
     */
    public function addNode(Node $node): Graph
    {
        $this->nodes[$node->getId()] = $node;
        return $this;
    }

    /**
     * @param Link $link
     * @return Graph
     */
    public function addLink(Link $link): Graph
    {
        $this->links[] = $link;
        return $this;
    }

    /**
     * @param string $sourceNodeId
     * @param string $targetNodeId
     * @param string $text
     * @param int    $style
     * @return Graph
     */
    public function addLinkByIds(
        string $sourceNodeId,
        string $targetNodeId,
        string $text = '',
        int $style = Link::ARROW
    ): Graph {
        $source = $this->getNode($sourceNodeId);
        if (!$source) {
            throw new Exception("Source node id=\"{$sourceNodeId}\" not found");
        }

        $target = $this->getNode($targetNodeId);
        if (!$target) {
            throw new Exception("Target node id=\"{$targetNodeId}\" not found");
        }

        return $this->addLink(new Link($source, $target, $text, $style));
    }

    /**
     * @param string $style
     * @return Graph
     */
    public function addStyle(string $style): Graph
    {
        $this->styles[] = $style;
        return $this;
    }

    /**
     * @param array<String> $params
     * @return string
     */
    public function renderHtml(array $params = []): string
    {
        return Render::html($this, $params);
    }

    /**
     * @return string
     */
    public function getLiveEditorUrl(): string
    {
        return Helper::getLiveEditorUrl($this);
    }

    /**
     * @param array $params
     * @return Graph
     */
    public function setParams(array $params): Graph
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param Graph $subGraph
     * @return Graph
     */
    public function addSubGraph(Graph $subGraph): Graph
    {
        $this->subGraphs[] = $subGraph;
        return $this;
    }

    /**
     * @param string $nodeId
     * @return Node|null
     */
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
