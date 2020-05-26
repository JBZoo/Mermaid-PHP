<?php

/**
 * JBZoo Toolbox - MermaidPHP
 *
 * This file is part of the JBZoo Toolbox project.
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
 * Class FlowchartTest
 * @package JBZoo\PHPUnit
 */
class FlowchartTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();
        Node::safeMode(false); // Set to default  value before each test case
    }

    public function testGraphRendering()
    {
        $graph = new Graph();

        isSame([
            'abc_order' => false,
            'title'     => 'Graph',
            'direction' => 'TB',
        ], $graph->getParams());
        isSame('graph TB;', (string)$graph);

        isSame([
            'abc_order' => false,
            'title'     => 'Graph',
            'direction' => 'LR',
        ], $graph->setParams(['direction' => Graph::LEFT_RIGHT])->getParams());
        isSame('graph LR;', (string)$graph);

        isSame([
            'abc_order' => false,
            'title'     => 'Graph',
            'direction' => 'TB',
        ], $graph->setParams(['direction' => Graph::TOP_BOTTOM])->getParams());
        isSame('graph TB;', (string)$graph);

        isSame([
            'abc_order' => false,
            'title'     => 'Graph',
            'direction' => 'TB',
        ], $graph->setParams(['direction' => Graph::TOP_BOTTOM])->getParams());
        isSame('graph TB;', (string)$graph);

        isSame([
            'abc_order' => false,
            'title'     => 'Graph',
            'direction' => 'RL',
        ], $graph->setParams(['direction' => Graph::RIGHT_LEFT])->getParams());
        isSame('graph RL;', (string)$graph);

        isSame([
            'abc_order' => true,
            'title'     => 'Graph',
            'direction' => 'BT',
        ], $graph->setParams(['direction' => Graph::BOTTOM_TOP, 'abc_order' => true])->getParams());
        isSame('graph BT;', (string)$graph);
    }

    public function testNodeRendering()
    {
        isSame('A("A");', (string)(new Node('A')));
        isSame('A', (new Node('A'))->getTitle());
        isSame('Title', (new Node('A'))->setTitle('Title')->getTitle());
        isSame('(%s)', (new Node('A'))->getForm());
        isSame('>%s]', (new Node('A'))->setForm(Node::ASYMMETRIC_SHAPE)->getForm());
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
        isSame('A[("Node Name")];', (string)(new Node('A', 'Node Name', Node::DATABASE)));
        isSame('A(["Node Name"]);', (string)(new Node('A', 'Node Name', Node::STADIUM)));
        isSame('A[["Node Name"]];', (string)(new Node('A', 'Node Name', Node::SUBROUTINE)));

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
        isSame('A-->|"This is the text"|B;', (string)$link->setStyle(Link::ARROW));
        isSame('A---|"This is the text"|B;', (string)$link->setStyle(Link::LINE));
        isSame('A == "This is the text" ==> B;', (string)$link->setStyle(Link::THICK));
        isSame('A-. "This is the text" .-> B;', (string)$link->setStyle(Link::DOTTED));
    }

    public function testNotFoundNode()
    {
        $graph = new Graph();
        isSame(null, $graph->getNode('undefined'));
    }

    public function testSimpleGraph()
    {
        $graph = new Graph(['abc_order' => false]);
        $nodeA = new Node('A', 'Text', Node::CIRCLE);
        $nodeB = new Node('B', 'Another text', Node::ROUND);
        $graph->addNode($nodeB);
        $graph->addNode($nodeA);
        $graph->addLink(new Link($nodeB, $nodeA, '$150 000.00'));
        $graph->addLink(new Link($nodeA, $nodeB, '$250 000.00'));

        isCount(2, $graph->getNodes());

        is(implode(PHP_EOL, [
            'graph TB;',
            '    B("Another text");',
            '    A(("Text"));',
            '',
            '    B-->|"$150 000.00"|A;',
            '    A-->|"$250 000.00"|B;',
            '',
        ]), (string)$graph);
    }

    public function testComplexGraph()
    {
        $graph = (new Graph(['abc_order' => true]))
            ->addSubGraph($subGraph1 = new Graph(['title' => 'Main workflow']))
            ->addSubGraph($subGraph2 = new Graph(['title' => 'Problematic workflow']))
            ->addStyle('linkStyle default interpolate basis');

        $subGraph1
            ->addNode($nodeE = new Node('E', 'Result two', Node::SQUARE))
            ->addNode($nodeB = new Node('B', 'Round edge', Node::ROUND))
            ->addNode($nodeA = new Node('A', 'Hard edge', Node::SQUARE))
            ->addNode($nodeC = new Node('C', 'Decision', Node::CIRCLE))
            ->addNode($nodeD = new Node('D', 'Result one', Node::SQUARE))
            ->addLink(new Link($nodeE, $nodeD))
            ->addLink(new Link($nodeB, $nodeC))
            ->addLink(new Link($nodeC, $nodeD, 'A double quote:"'))
            ->addLink(new Link($nodeC, $nodeE, 'A dec char:♥'))
            ->addLink(new Link($nodeA, $nodeB, ' Link text<br>/\\!@#$%^&*()_+><\' " '));

        $subGraph2
            ->addNode($alone = new Node('alone', 'Alone'))
            ->addLink(new Link($alone, $nodeC));

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    subgraph "Main workflow"',
            '        E["Result two"];',
            '        B("Round edge");',
            '        A["Hard edge"];',
            '        C(("Decision"));',
            '        D["Result one"];',
            '        E-->D;',
            '        B-->C;',
            '        C-->|"A double quote:#quot;"|D;',
            '        C-->|"A dec char:#hearts;"|E;',
            '        A-->|"Link text<br>/\!@#$%^#amp;*()_+><\' #quot;"|B;',
            '    end',
            '    subgraph "Problematic workflow"',
            '        alone("Alone");',
            '        alone-->C;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), (string)$graph);

        $html = $graph->renderHtml();
        isContain($graph, $html);
    }

    public function testComplexGraphSafeMode()
    {
        Node::safeMode(true);

        $graph = (new Graph(['abc_order' => true]))
            ->addSubGraph($subGraph1 = new Graph(['title' => 'Main workflow']))
            ->addSubGraph($subGraph2 = new Graph(['title' => 'Problematic workflow']))
            ->addStyle('linkStyle default interpolate basis');

        $subGraph1
            ->addNode($nodeE = new Node('E', 'Result two', Node::SQUARE))
            ->addNode($nodeB = new Node('B', 'Round edge', Node::ROUND))
            ->addNode($nodeA = new Node('A', 'Hard edge', Node::SQUARE))
            ->addNode($nodeC = new Node('C', 'Decision', Node::CIRCLE))
            ->addNode($nodeD = new Node('D', 'Result one', Node::SQUARE))
            ->addLink(new Link($nodeE, $nodeD))
            ->addLink(new Link($nodeB, $nodeC))
            ->addLink(new Link($nodeC, $nodeD, 'A double quote:"'))
            ->addLink(new Link($nodeC, $nodeE, 'A dec char:♥'))
            ->addLink(new Link($nodeA, $nodeB, ' Link text<br>/\\!@#$%^&*()_+><\' " '));

        $subGraph2
            ->addNode($alone = new Node('alone', 'Alone'))
            ->addLink(new Link($alone, $nodeC));

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    subgraph "Main workflow"',
            '        3a3ea00cfc35332cedf6e5e9a32e94da["Result two"];',
            '        9d5ed678fe57bcca610140957afab571("Round edge");',
            '        7fc56270e7a70fa81a5935b72eacbe29["Hard edge"];',
            '        0d61f8370cad1d412f80b84d143e1257(("Decision"));',
            '        f623e75af30e62bbd73d6df5b50bb7b5["Result one"];',
            '        3a3ea00cfc35332cedf6e5e9a32e94da-->f623e75af30e62bbd73d6df5b50bb7b5;',
            '        9d5ed678fe57bcca610140957afab571-->0d61f8370cad1d412f80b84d143e1257;',
            '        0d61f8370cad1d412f80b84d143e1257-->|"A double quote:#quot;"|f623e75af30e62bbd73d6df5b50bb7b5;',
            '        0d61f8370cad1d412f80b84d143e1257-->|"A dec char:#hearts;"|3a3ea00cfc35332cedf6e5e9a32e94da;',
            '        7fc56270e7a70fa81a5935b72eacbe29-->' .
            '|"Link text<br>/\!@#$%^#amp;*()_+><\' #quot;"|9d5ed678fe57bcca610140957afab571;',
            '    end',
            '    subgraph "Problematic workflow"',
            '        c42bbd90740264d115048a82c9a10214("Alone");',
            '        c42bbd90740264d115048a82c9a10214-->0d61f8370cad1d412f80b84d143e1257;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), (string)$graph);

        $html = $graph->renderHtml();
        isContain($graph, $html);
    }

    public function testSimpleSubGraph()
    {
        $graphMain = new Graph();

        $graphMain->addSubGraph($subOne = new Graph(['title' => 'one']));
        $graphMain->addSubGraph($subTwo = new Graph(['title' => 'two']));
        $graphMain->addSubGraph($subThree = new Graph(['title' => 'three']));

        $subOne->addNode(new Node('a1'))
            ->addNode($a2 = new Node('a2'))
            ->addLinkByIds('a1', 'a2');

        $subTwo->addNode(new Node('b1'))
            ->addNode(new Node('b2'))
            ->addLinkByIds('b1', 'b2');

        $subThree->addNode($c1 = new Node('c1'))
            ->addNode(new Node('c2'))
            ->addLinkByIds('c1', 'c2');

        $graphMain->addLink(new Link($c1, $a2));

        $this->dumpHtml($graphMain);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    c1-->a2;',
            '',
            '    subgraph "one"',
            '        a1("a1");',
            '        a2("a2");',
            '        a1-->a2;',
            '    end',
            '    subgraph "two"',
            '        b1("b1");',
            '        b2("b2");',
            '        b1-->b2;',
            '    end',
            '    subgraph "three"',
            '        c1("c1");',
            '        c2("c2");',
            '        c1-->c2;',
            '    end',
        ]), (string)$graphMain);
    }

    public function testSimpleSubGraphSafeMode()
    {
        Node::safeMode(true);

        $graphMain = new Graph();

        $graphMain->addSubGraph($subOne = new Graph(['title' => 'one']));
        $graphMain->addSubGraph($subTwo = new Graph(['title' => 'two']));
        $graphMain->addSubGraph($subThree = new Graph(['title' => 'three']));

        $subOne->addNode(new Node('a1'))
            ->addNode($a2 = new Node('a2'))
            ->addLinkByIds('a1', 'a2');

        $subTwo->addNode(new Node('b1'))
            ->addNode(new Node('b2'))
            ->addLinkByIds('b1', 'b2');

        $subThree->addNode($c1 = new Node('c1'))
            ->addNode(new Node('c2'))
            ->addLinkByIds('c1', 'c2');

        $graphMain->addLink(new Link($c1, $a2));

        $this->dumpHtml($graphMain);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    a9f7e97965d6cf799a529102a973b8b9-->693a9fdd4c2fd0700968fba0d07ff3c0;',
            '',
            '    subgraph "one"',
            '        8a8bb7cd343aa2ad99b7d762030857a2("a1");',
            '        693a9fdd4c2fd0700968fba0d07ff3c0("a2");',
            '        8a8bb7cd343aa2ad99b7d762030857a2-->693a9fdd4c2fd0700968fba0d07ff3c0;',
            '    end',
            '    subgraph "two"',
            '        edbab45572c72a5d9440b40bcc0500c0("b1");',
            '        fbfba2e45c2045dc5cab22a5afe83d9d("b2");',
            '        edbab45572c72a5d9440b40bcc0500c0-->fbfba2e45c2045dc5cab22a5afe83d9d;',
            '    end',
            '    subgraph "three"',
            '        a9f7e97965d6cf799a529102a973b8b9("c1");',
            '        9ab62b5ef34a985438bfdf7ee0102229("c2");',
            '        a9f7e97965d6cf799a529102a973b8b9-->9ab62b5ef34a985438bfdf7ee0102229;',
            '    end',
        ]), (string)$graphMain);
    }

    public function testBasicFlowchart()
    {
        $graph = (new Graph(['direction' => Graph::LEFT_RIGHT]))
            ->addNode(new Node('A', 'Square Rect', Node::SQUARE))
            ->addNode(new Node('B', 'Circle', Node::CIRCLE))
            ->addNode(new Node('C', 'Round Rect'))
            ->addNode(new Node('D', 'Rhombus', Node::RHOMBUS))
            ->addLinkByIds('A', 'B', 'Link text')
            ->addLinkByIds('A', 'C')
            ->addLinkByIds('B', 'D')
            ->addLinkByIds('C', 'D');

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph LR;',
            '    A["Square Rect"];',
            '    B(("Circle"));',
            '    C("Round Rect");',
            '    D{"Rhombus"};',
            '',
            '    A-->|"Link text"|B;',
            '    A-->C;',
            '    B-->D;',
            '    C-->D;',
            '',
        ]), (string)$graph);
    }

    public function testLargerFlowchartWithSomeStyling()
    {
        $graph = (new Graph(['abc_order' => true]))
            ->addNode(new Node('sq', 'Square shape', Node::SQUARE))
            ->addNode(new Node('ci', 'Circle shape', Node::CIRCLE))
            ->addLinkByIds('sq', 'ci');

        $subGraphA = (new Graph(['title' => 'A', 'abc_order' => true]))
            ->addNode(new Node('od', 'Odd shape', Node::ASYMMETRIC_SHAPE))
            ->addNode(new Node('ro', 'Rounded<br>square<br>shape', Node::ROUND))
            ->addNode(new Node('ro2', 'Rounded square shape', Node::ROUND))
            ->addNode(new Node('di', 'Diamond with <br/> line break', Node::RHOMBUS))
            ->addLinkByIds('od', 'ro', 'Two line<br/>edge comment')
            ->addLinkByIds('di', 'ro', '', Link::DOTTED)
            ->addLinkByIds('di', 'ro2', '', Link::THICK);
        $graph->addSubGraph($subGraphA);

        $graph->addStyle('classDef green fill:#9f6,stroke:#333,stroke-width:2px');
        $graph->addStyle('classDef orange fill:#f96,stroke:#333,stroke-width:4px');
        $graph->addStyle('class sq,e green');
        $graph->addStyle('class di orange');

        $graph
            ->addNode(new Node('e', 'Inner / circle<br>and some odd <br>special characters', Node::CIRCLE))
            ->addNode(new Node('od3', 'Really long text with linebreak<br>in an Odd shape', Node::CIRCLE))
            ->addNode(new Node('cyr', 'Cyrillic', Node::SQUARE))
            ->addNode(new Node('cyr2', 'Circle shape', Node::CIRCLE))
            ->addNode(new Node('f', ',.?!+-*ز'))
            ->addLinkByIds('cyr', 'cyr2')
            ->addLinkByIds('e', 'od3')
            ->addLinkByIds('e', 'f');

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    ci(("Circle shape"));',
            '    cyr2(("Circle shape"));',
            '    cyr["Cyrillic"];',
            '    e(("Inner / circle<br>and some odd <br>special characters"));',
            '    f(",.?!+-*ز");',
            '    od3(("Really long text with linebreak<br>in an Odd shape"));',
            '    sq["Square shape"];',
            '',
            '    cyr-->cyr2;',
            '    e-->f;',
            '    e-->od3;',
            '    sq-->ci;',
            '',
            '    subgraph "A"',
            '        di{"Diamond with <br/> line break"};',
            '        od>"Odd shape"];',
            '        ro("Rounded<br>square<br>shape");',
            '        ro2("Rounded square shape");',
            '        di ==> ro2;',
            '        di-.->ro;',
            '        od-->|"Two line<br/>edge comment"|ro;',
            '    end',
            'classDef green fill:#9f6,stroke:#333,stroke-width:2px;',
            'classDef orange fill:#f96,stroke:#333,stroke-width:4px;',
            'class sq,e green;',
            'class di orange;',
        ]), (string)$graph);
    }

    public function testNestedGraph()
    {
        $graph = new Graph();
        $graph->addSubGraph($globalGraph = new Graph(['title' => 'Global']));
        $globalGraph->addNode($alone = new Node('Alone'));
        $globalGraph->addLink(new Link($alone, $alone));
        $globalGraph->addSubGraph($subGraph = new Graph(['title' => 'Sub Graph']));
        $subGraph->addSubGraph($subSubGraph = new Graph(['title' => 'Sub Sub Graph']));

        $subGraph->addNode($nodeA = new Node('A', 'State A'));
        $subGraph->addNode($nodeB = new Node('B', 'State B'));
        $subGraph->addLinkByIds('A', 'B');

        $subSubGraph->addNode($nodeC = new Node('C', 'State C'));
        $subSubGraph->addNode($nodeD = new Node('D', 'State D'));
        $subSubGraph->addLinkByIds('C', 'D');

        $globalGraph->addLink(new Link($nodeA, $nodeC));
        $globalGraph->addLink(new Link($nodeA, $nodeD));
        $globalGraph->addLink(new Link($nodeB, $nodeD));

        $this->dumpHtml($graph);

        is(implode(PHP_EOL, [
            'graph TB;',
            '    subgraph "Global"',
            '        Alone("Alone");',
            '        Alone-->Alone;',
            '        A-->C;',
            '        A-->D;',
            '        B-->D;',
            '        subgraph "Sub Graph"',
            '            A("State A");',
            '            B("State B");',
            '            A-->B;',
            '            subgraph "Sub Sub Graph"',
            '                C("State C");',
            '                D("State D");',
            '                C-->D;',
            '            end',
            '        end',
            '    end',
        ]), (string)$graph);
    }

    public function testCheckReadmeExample()
    {
        $graph = (new Graph(['abc_order' => true]))
            ->addSubGraph($subGraph1 = new Graph(['title' => 'Main workflow']))
            ->addSubGraph($subGraph2 = new Graph(['title' => 'Problematic workflow']))
            ->addStyle('linkStyle default interpolate basis');

        $subGraph1
            ->addNode($nodeE = new Node('E', 'Result two', Node::SQUARE))
            ->addNode($nodeB = new Node('B', 'Round edge', Node::ROUND))
            ->addNode($nodeA = new Node('A', 'Hard edge', Node::SQUARE))
            ->addNode($nodeC = new Node('C', 'Decision', Node::CIRCLE))
            ->addNode($nodeD = new Node('D', 'Result one', Node::SQUARE))
            ->addLink(new Link($nodeE, $nodeD))
            ->addLink(new Link($nodeB, $nodeC))
            ->addLink(new Link($nodeC, $nodeD, 'A double quote:"'))
            ->addLink(new Link($nodeC, $nodeE, 'A dec char:♥'))
            ->addLink(new Link($nodeA, $nodeB, ' Link text<br>/\\!@#$%^&*()_+><\' " '));

        $subGraph2
            ->addNode($alone = new Node('alone', 'Alone'))
            ->addLink(new Link($alone, $nodeC));

        isContain((string)$graph, file_get_contents(PROJECT_ROOT . '/README.md'));
        $this->dumpHtml($graph);

        isContain($graph->getLiveEditorUrl(), file_get_contents(PROJECT_ROOT . '/README.md'));
    }

    /**
     * @param Graph $graph
     */
    protected function dumpHtml(Graph $graph)
    {
        file_put_contents(
            PROJECT_ROOT . '/build/index.html',
            $graph->renderHtml(['debug' => true, 'title' => $this->getName()])
        );
    }
}
