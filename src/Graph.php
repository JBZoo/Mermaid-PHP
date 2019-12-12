<?php
/**
 * JBZoo MermaidPHP
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    MermaidPHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/MermaidPHP
 */

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

    /**
     * @var Graph[]
     */
    protected $subGraphs = [];

    /**
     * @var Node[]
     */
    protected $nodes = [];

    /**
     * @var Link[]
     */
    protected $links = [];

    /**
     * @var mixed[]
     */
    protected $params = [
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
        $spaces = str_repeat(' ', $shift);
        $spacesSub = str_repeat(' ', $shift + 4);

        if ($isMainGraph) {
            $result = ["graph {$this->params['direction']};"];
        } else {
            $result = ["{$spaces}subgraph " . Helper::escape($this->params['title'])];
        }

        if (count($this->nodes) > 0) {
            $tmp = [];
            foreach ($this->nodes as $node) {
                $tmp[] = $spacesSub . $node;
            }
            if ($this->params['abc_order']) {
                sort($tmp);
            }
            $result = array_merge($result, $tmp);
            if ($isMainGraph) {
                $result[] = '';
            }
        }

        if (count($this->links) > 0) {
            $tmp = [];
            foreach ($this->links as $link) {
                $tmp[] = $spacesSub . $link;
            }
            if ($this->params['abc_order']) {
                sort($tmp);
            }
            $result = array_merge($result, $tmp);
            if ($isMainGraph) {
                $result[] = '';
            }
        }

        foreach ($this->subGraphs as $subGraph) {
            $result[] = $subGraph->render(false, $shift + 4);
        }

        if ($isMainGraph && count($this->styles) > 0) {
            foreach ($this->styles as $style) {
                $result[] = $spaces . $style . ';';
            }
        }

        if (!$isMainGraph) {
            $result[] = "{$spaces}end";
        }

        return implode(PHP_EOL, $result);
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
        return $this->addLink(new Link($this->getNode($sourceNodeId), $this->getNode($targetNodeId), $text, $style));
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
        return Helper::renderHtml($this, $params);
    }

    /**
     * @param mixed[] $params
     * @return Graph
     */
    public function setParams(array $params): Graph
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @return mixed[]
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
     * @return Node
     */
    public function getNode(string $nodeId): Node
    {
        $node = $this->nodes[$nodeId] ?? null;
        if (!$node) {
            throw new Exception("Node with id={$nodeId} not found");
        }

        return $node;
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
