# JBZoo Mermaid-PHP  [![Latest Stable Version](https://poser.pugx.org/JBZoo/Mermaid-PHP/v/stable)](https://packagist.org/packages/JBZoo/Mermaid-PHP) [![License](https://poser.pugx.org/JBZoo/Mermaid-PHP/license)](https://packagist.org/packages/JBZoo/Mermaid-PHP) [![Build Status](https://travis-ci.org/JBZoo/Mermaid-PHP.svg?branch=master)](https://travis-ci.org/JBZoo/Mermaid-PHP) [![Coverage Status](https://coveralls.io/repos/github/JBZoo/Mermaid-PHP/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Mermaid-PHP?branch=master)

### Usage

```php
<?php

use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Link;
use JBZoo\MermaidPHP\Node;

require_once './vendor/autoload.php';

$graph = (new Graph())
    ->addNode($nodeA = new Node('A', 'Hard edge', Node::SQUARE))
    ->addNode($nodeB = new Node('B', 'Round edge', Node::ROUND))
    ->addNode($nodeC = new Node('C', 'Decision', Node::RHOMBUS))
    ->addNode($nodeD = new Node('D', 'Result one', Node::SQUARE))
    ->addNode($nodeE = new Node('E', 'Result two', Node::SQUARE))
    ->addLink(new Link($nodeA, $nodeB, 'Link text'))
    ->addLink(new Link($nodeB, $nodeC))
    ->addLink(new Link($nodeC, $nodeD, 'One'))
    ->addLink(new Link($nodeC, $nodeE, 'Two'))
    ->addStyle('linkStyle default interpolate basis');

echo $graph; // Get result as string (or $graph->__toString())
$htmlCode = $graph->renderHtml(true, '8.4.3'); // Get result as HTML code for debugging 
```

### Result

[Mermaid Live Editor](https://mermaidjs.github.io/mermaid-live-editor/#/edit/eyJjb2RlIjoiZ3JhcGggTFI7XG4gICAgQVtcIkhhcmQgZWRnZVwiXTtcbiAgICBCKFwiUm91bmQgZWRnZVwiKTtcbiAgICBDe1wiRGVjaXNpb25cIn07XG4gICAgRFtcIlJlc3VsdCBvbmVcIl07XG4gICAgRVtcIlJlc3VsdCB0d29cIl07XG4gICAgQS0tPnxMaW5rIHRleHR8QjtcbiAgICBCLS0-QztcbiAgICBDLS0-fE9uZXxEO1xuICAgIEMtLT58VHdvfEU7XG5saW5rU3R5bGUgZGVmYXVsdCBpbnRlcnBvbGF0ZSBiYXNpczsiLCJtZXJtYWlkIjp7InRoZW1lIjoiZm9yZXN0In19)

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
linkStyle default interpolate basis
```

### See also
 - [Mermaid on GitHub](https://github.com/knsv/mermaid)
 - [Mermaid Documentation](https://mermaidjs.github.io/)

## Unit tests and check code style
```sh
make
make test-all
```


## License

MIT
