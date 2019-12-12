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

namespace JBZoo\PHPUnit;

use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Link;
use JBZoo\MermaidPHP\Node;

/**
 * Class Flowchart
 * @package JBZoo\PHPUnit
 */
class FlowchartTest extends PHPUnit
{
    public function testGraphRendering()
    {
        $graph = new Graph();
        isSame(Graph::LEFT_RIGHT, $graph->getDirection());
        isSame('graph LR;', (string)$graph);

        isSame(Graph::LEFT_RIGHT, $graph->setDirection(Graph::LEFT_RIGHT)->getDirection());
        isSame('graph LR;', (string)$graph);

        isSame(Graph::TOP_BOTTOM, $graph->setDirection(Graph::TOP_BOTTOM)->getDirection());
        isSame('graph TB;', (string)$graph);

        isSame(Graph::RIGHT_LEFT, $graph->setDirection(Graph::RIGHT_LEFT)->getDirection());
        isSame('graph RL;', (string)$graph);

        isSame(Graph::BOTTOM_TOP, $graph->setDirection(Graph::BOTTOM_TOP)->getDirection());
        isSame('graph BT;', (string)$graph);
    }

    public function testNodeRendering()
    {
        isSame('A;', (string)(new Node('A')));
        isSame('A', (new Node('A'))->getTitle());
        isSame('Title', (new Node('A'))->setTitle('Title')->getTitle());
        isSame('("%s")', (new Node('A'))->getForm());
        isSame('>"%s"]', (new Node('A'))->setForm(Node::ASYMMETRIC_SHAPE)->getForm());
        isSame('A("Node Name");', (string)(new Node('A', 'Node Name')));
        isSame('A("Node Name");', (string)(new Node('A', 'Node Name', Node::ROUND)));
        isSame('A{"Node Name"};', (string)(new Node('A', 'Node Name', Node::RHOMBUS)));
        isSame('A(("Node Name"));', (string)(new Node('A', 'Node Name', Node::CIRCLE)));
        isSame('A>"Node Name"];', (string)(new Node('A', 'Node Name', Node::ASYMMETRIC_SHAPE)));
        isSame('A{{"Node Name"}};', (string)(new Node('A', 'Node Name', Node::HEXAGON)));
        isSame('A[/"Node Name"/];', (string)(new Node('A', 'Node Name', Node::PARALLELOGRAM)));
        isSame('A[\"Node Name"\];', (string)(new Node('A', 'Node Name', Node::PARALLELOGRAM_ALT)));
        isSame('A[/"Node Name"\];', (string)(new Node('A', 'Node Name', Node::TRAPEZOID)));
        isSame('A[\"Node Name"/];', (string)(new Node('A', 'Node Name', Node::TRAPEZOID_ALT)));

        isSame('A("This is the (text) in the box");', (string)(new Node('A', 'This is the (text) in the box')));
        isSame('A("A double quote:#quot;");', (string)(new Node('A', 'A double quote:"')));
        isSame('A("A dec char:#hearts;");', (string)(new Node('A', 'A dec char:♥')));
    }

    public function testLinkRendering()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $link = new Link($nodeA, $nodeB);

        isSame('A-->B;', (string)$link);
        isSame('A-->B;', (string)$link->setStyle(Link::ARROW));
        isSame('A --- B;', (string)$link->setStyle(Link::LINE));
        isSame('A ==> B;', (string)$link->setStyle(Link::THICK));
        isSame('A-.->B;', (string)$link->setStyle(Link::DOTTED));

        $link->setText('This is the text');
        isSame('A-->|This is the text|B;', (string)$link->setStyle(Link::ARROW));
        isSame('A---|This is the text|B;', (string)$link->setStyle(Link::LINE));
        isSame('A == This is the text ==> B;', (string)$link->setStyle(Link::THICK));
        isSame('A-. This is the text .-> B;', (string)$link->setStyle(Link::DOTTED));
    }

    public function testSimpleGraph()
    {
        $graph = new Graph();
        $nodeA = new Node('A', 'Text', Node::CIRCLE);
        $nodeB = new Node('B', 'Another text', Node::ROUND);
        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addLink(new Link($nodeA, $nodeB, '$250 000.00'));
        $graph->addLink(new Link($nodeB, $nodeA, '$150 000.00'));

        is(implode(PHP_EOL, [
            'graph LR;',
            '    A(("Text"));',
            '    B("Another text");',
            '    A-->|$250 000.00|B;',
            '    B-->|$150 000.00|A;'
        ]), (string)$graph);
    }

    public function testComplexGraph()
    {
        $graph = new Graph();
        $graph->addNode($nodeA = new Node('A', 'Hard edge', Node::SQUARE));
        $graph->addNode($nodeB = new Node('B', 'Round edge', Node::ROUND));
        $graph->addNode($nodeC = new Node('C', 'Decision', Node::RHOMBUS));
        $graph->addNode($nodeD = new Node('D', 'Result one', Node::SQUARE));
        $graph->addNode($nodeE = new Node('E', 'Result two', Node::SQUARE));

        $graph->addLink(new Link($nodeA, $nodeB, 'Link text'));
        $graph->addLink(new Link($nodeB, $nodeC));
        $graph->addLink(new Link($nodeC, $nodeD, 'One'));
        $graph->addLink(new Link($nodeC, $nodeE, 'Two'));
        $graph->addStyle('linkStyle default interpolate basis');

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph LR;',
            '    A["Hard edge"];',
            '    B("Round edge");',
            '    C{"Decision"};',
            '    D["Result one"];',
            '    E["Result two"];',
            '    A-->|Link text|B;',
            '    B-->C;',
            '    C-->|One|D;',
            '    C-->|Two|E;',
            'linkStyle default interpolate basis',
        ]), (string)$graph);

        $html = $graph->renderHtml();
        isContain($graph, $html);
        isContain('<script>mermaid.initialize(', $html);
    }

    /**
     * @param Graph $graph
     */
    protected function dumpHtml(Graph $graph)
    {
        file_put_contents(PATH_ROOT . '/build/index.html', $graph->renderHtml(true));
    }
}
