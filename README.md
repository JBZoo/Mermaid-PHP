# JBZoo / Mermaid-PHP

[![CI](https://github.com/JBZoo/Mermaid-PHP/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/JBZoo/Mermaid-PHP/actions/workflows/main.yml?query=branch%3Amaster)    [![Coverage Status](https://coveralls.io/repos/github/JBZoo/Mermaid-PHP/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Mermaid-PHP?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Mermaid-PHP/coverage.svg)](https://shepherd.dev/github/JBZoo/Mermaid-PHP)    [![Psalm Level](https://shepherd.dev/github/JBZoo/Mermaid-PHP/level.svg)](https://shepherd.dev/github/JBZoo/Mermaid-PHP)    [![CodeFactor](https://www.codefactor.io/repository/github/jbzoo/mermaid-php/badge)](https://www.codefactor.io/repository/github/jbzoo/mermaid-php/issues)    
[![Stable Version](https://poser.pugx.org/jbzoo/mermaid-php/version)](https://packagist.org/packages/jbzoo/mermaid-php/)    [![Total Downloads](https://poser.pugx.org/jbzoo/mermaid-php/downloads)](https://packagist.org/packages/jbzoo/mermaid-php/stats)    [![Dependents](https://poser.pugx.org/jbzoo/mermaid-php/dependents)](https://packagist.org/packages/jbzoo/mermaid-php/dependents?order_by=downloads)    [![Visitors](https://visitor-badge.glitch.me/badge?page_id=jbzoo.mermaid-php)]()    [![GitHub License](https://img.shields.io/github/license/jbzoo/mermaid-php)](https://github.com/JBZoo/Mermaid-PHP/blob/master/LICENSE)



Generate diagrams and flowcharts as HTML which is based on [mermaid-js](https://mermaid.js.org/).


### Usage

```php
<?php

use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Link;
use JBZoo\MermaidPHP\Node;
use JBZoo\MermaidPHP\Render;

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

echo $graph; // Get result as string (or $graph->__toString(), or (string)$graph)
$htmlCode = $graph->renderHtml([
    'debug'       => true,
    'theme'       => Render::THEME_DARK,
    'title'       => 'Example',
    'show-zoom'   => false,
    'mermaid_url' => 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs',
]); // Get result as HTML code for debugging

echo $graph->getLiveEditorUrl(); // Get link to live editor 
```

### Result
[Open live editor](https://mermaid-js.github.io/mermaid-live-editor/#/edit/eyJjb2RlIjoiZ3JhcGggVEI7XG4gICAgc3ViZ3JhcGggXCJNYWluIHdvcmtmbG93XCJcbiAgICAgICAgRVtcIlJlc3VsdCB0d29cIl07XG4gICAgICAgIEIoXCJSb3VuZCBlZGdlXCIpO1xuICAgICAgICBBW1wiSGFyZCBlZGdlXCJdO1xuICAgICAgICBDKChcIkRlY2lzaW9uXCIpKTtcbiAgICAgICAgRFtcIlJlc3VsdCBvbmVcIl07XG4gICAgICAgIEUtLT5EO1xuICAgICAgICBCLS0+QztcbiAgICAgICAgQy0tPnxcIkEgZG91YmxlIHF1b3RlOiNxdW90O1wifEQ7XG4gICAgICAgIEMtLT58XCJBIGRlYyBjaGFyOiNoZWFydHM7XCJ8RTtcbiAgICAgICAgQS0tPnxcIkxpbmsgdGV4dDxicj5cL1xcIUAjJCVeI2FtcDsqKClfKz48JyAjcXVvdDtcInxCO1xuICAgIGVuZFxuICAgIHN1YmdyYXBoIFwiUHJvYmxlbWF0aWMgd29ya2Zsb3dcIlxuICAgICAgICBhbG9uZShcIkFsb25lXCIpO1xuICAgICAgICBhbG9uZS0tPkM7XG4gICAgZW5kXG5saW5rU3R5bGUgZGVmYXVsdCBpbnRlcnBvbGF0ZSBiYXNpczsiLCJtZXJtYWlkIjp7InRoZW1lIjoiZm9yZXN0In19)

```
graph TB;
    subgraph "Main workflow"
        E["Result two"];
        B("Round edge");
        A["Hard edge"];
        C(("Decision"));
        D["Result one"];
        E-->D;
        B-->C;
        C-->|"A double quote:#quot;"|D;
        C-->|"A dec char:#hearts;"|E;
        A-->|"Link text<br>/\!@#$%^#amp;*()_+><' #quot;"|B;
    end
    subgraph "Problematic workflow"
        alone("Alone");
        alone-->C;
    end
linkStyle default interpolate basis;
```


### See also
 - [Mermaid on GitHub](https://github.com/mermaid-js/mermaid)
 - [Mermaid Documentation](https://mermaid.js.org/)


## Unit tests and check code style
```sh
make update
make test-all
```


## License

MIT


## See Also

- [CI-Report-Converter](https://github.com/JBZoo/CI-Report-Converter) - Converting different error reports for deep compatibility with popular CI systems.
- [Composer-Diff](https://github.com/JBZoo/Composer-Diff) - See what packages have changed after `composer update`.
- [Composer-Graph](https://github.com/JBZoo/Composer-Graph) - Dependency graph visualization of composer.json based on mermaid-js.
- [Utils](https://github.com/JBZoo/Utils) - Collection of useful PHP functions, mini-classes, and snippets for every day.
- [Image](https://github.com/JBZoo/Image) - Package provides object-oriented way to manipulate with images as simple as possible.
- [Data](https://github.com/JBZoo/Data) - Extended implementation of ArrayObject. Use files as config/array. 
- [Retry](https://github.com/JBZoo/Retry) - Tiny PHP library providing retry/backoff functionality with multiple backoff strategies and jitter support.
- [SimpleTypes](https://github.com/JBZoo/SimpleTypes) - Converting any values and measures - money, weight, exchange rates, length, ...
