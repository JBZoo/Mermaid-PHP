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

namespace JBZoo\PHPUnit;

use JBZoo\MermaidPHP\ERDiagram\Entity;
use JBZoo\MermaidPHP\ERDiagram\ERDiagram;
use JBZoo\MermaidPHP\ERDiagram\Relation\ManyToMany;
use JBZoo\MermaidPHP\ERDiagram\Relation\ManyToOne;
use JBZoo\MermaidPHP\ERDiagram\Relation\OneToMany;
use JBZoo\MermaidPHP\ERDiagram\Relation\OneToOne;
use JBZoo\MermaidPHP\ERDiagram\Relation\Relation;

final class ERDiagramTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBasicErDiagram(): void
    {
        $diagram = (new ERDiagram(['title' => 'Order Example']));
        $diagram
            ->addEntity($customerEntity = new Entity('C', 'Customer'))
            ->addEntity($orderEntity = new Entity('O', 'Order'))
            ->addEntity($lineItemEntity = new Entity('LI', 'Line-Item'))
            ->addEntity($deliveryAddressEntity = new Entity('DA', 'Delivery-Address'))
            ->addEntity($creditCardEntity = new Entity('CC', 'Credit-Card'))
            ->addRelation(new OneToMany($customerEntity, $orderEntity, 'places', Relation::ONE_OR_MORE))
            ->addRelation(new ManyToOne($lineItemEntity, $orderEntity, 'belongs', Relation::ZERO_OR_MORE))
            ->addRelation(new ManyToMany($customerEntity, $deliveryAddressEntity, 'uses', Relation::ONE_OR_MORE))
            ->addRelation(new OneToOne($customerEntity, $creditCardEntity, 'has', Relation::ONE_OR_MORE))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            '---',
            'title: Order Example',
            '---',
            'erDiagram',
            '    "Customer" ||--|{ "Order" : places',
            '    "Line-Item" }o--|| "Order" : belongs',
            '    "Customer" }o--|{ "Delivery-Address" : uses',
            '    "Customer" ||--|| "Credit-Card" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToOne(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToOne($a, $b, 'has'))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToOneWithCardinality(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToOne($a, $b, 'has', Relation::ZERO_OR_ONE))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToMany(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToMany($a, $b, 'has'))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToManyWithCardinality(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToMany($a, $b, 'has', Relation::ZERO_OR_MORE))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--o{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToOne(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToOne($a, $b, 'has'))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--|| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToOneWithCardinality(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToOne($a, $b, 'has', Relation::ZERO_OR_ONE))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToMany(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToMany($a, $b, 'has'))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--o{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToManyWithCardinality(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToMany($a, $b, 'has', Relation::ONE_OR_MORE))
        ;

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--|{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    protected function dumpHtml(ERDiagram $diagram): void
    {
        \file_put_contents(
            PROJECT_ROOT . '/build/index.html',
            $diagram->renderHtml(['debug' => true, 'title' => $this->getName()]),
        );
    }
}
