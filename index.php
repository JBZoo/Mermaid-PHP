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

require __DIR__ . '/vendor/autoload.php';

use JBZoo\MermaidPHP\ERDiagram\Entity\Entity;
use JBZoo\MermaidPHP\ERDiagram\Entity\EntityProperty;
use JBZoo\MermaidPHP\ERDiagram\ERDiagram;
use JBZoo\MermaidPHP\ERDiagram\Relation\OneToOne;
use JBZoo\MermaidPHP\ERDiagram\Relation\OneToMany;
use JBZoo\MermaidPHP\ERDiagram\Relation\ManyToOne;
use JBZoo\MermaidPHP\ERDiagram\Relation\ManyToMany;
use JBZoo\MermaidPHP\ERDiagram\Relation\Relation;

$diagram = (new ERDiagram(['title' => 'Order Example']));

$diagram
    ->addEntity($customerEntity = new Entity('C', 'Customer', props: [
        new EntityProperty('id', 'int', [EntityProperty::PRIMARY_KEY], 'ID of user'),
        new EntityProperty('cash', 'float'),
    ]))
    ->addEntity($orderEntity = new Entity('O', 'Order'))
    ->addEntity($lineItemEntity = new Entity('LI', 'Line-Item'))
    ->addEntity($deliveryAddressEntity = new Entity('DA', 'Delivery-Address'))
    ->addEntity($creditCardEntity = new Entity('CC', 'Credit-Card'))
    ->addRelation(new OneToMany($customerEntity, $orderEntity, 'places', Relation::ONE_OR_MORE))
    ->addRelation(new ManyToOne($lineItemEntity, $orderEntity, 'belongs', Relation::ZERO_OR_MORE))
    ->addRelation(new ManyToMany($customerEntity, $deliveryAddressEntity, 'uses', Relation::ONE_OR_MORE))
    ->addRelation(new OneToOne($customerEntity, $creditCardEntity, 'has', Relation::ONE_OR_MORE))
;
//header('Content-Type: text/plain');
//echo $diagram; // Get result as string (or $graph->__toString(), or (string)$graph)
$htmlCode = $diagram->renderHtml([
    'debug'       => true,
    'theme'       => \JBZoo\MermaidPHP\Render::THEME_DARK,
    'title'       => 'Example',
    'show-zoom'   => false,
    'mermaid_url' => 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs',
]); // Get result as HTML code for debugging

echo $diagram->getLiveEditorUrl(); // Get link to live editor
