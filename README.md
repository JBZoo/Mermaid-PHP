# JBZoo Mermaid-PHP  [![Build Status](https://travis-ci.org/JBZoo/Mermaid-PHP.svg?branch=master)](https://travis-ci.org/JBZoo/Mermaid-PHP) [![Coverage Status](https://coveralls.io/repos/github/JBZoo/mermaid-php/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/mermaid-php?branch=master)

Example

```php
use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Link;
use JBZoo\MermaidPHP\Node;

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

echo $graph;
```

Result
```
graph LR;
    A["Hard edge"];
    B("Round edge");
    C{"Decision"};
    D["Result one"];
    E["Result two"];
    A-->|Link text|B;
    B-->C;
    C-->|One|D;
    C-->|Two|E;
```


## Unit tests and check code style
```sh
make
make test-all
```


## License

MIT
