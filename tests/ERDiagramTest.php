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

use JBZoo\MermaidPHP\ERDiagram\Entity\Entity;
use JBZoo\MermaidPHP\ERDiagram\Entity\EntityProperty;
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
            ->addRelation(new OneToOne($customerEntity, $creditCardEntity, 'has', Relation::ONE_OR_MORE));

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
            ->addRelation(new OneToOne($a, $b, 'has'));

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
            ->addRelation(new OneToOne($a, $b, 'has', Relation::ZERO_OR_ONE));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToOneNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToOne($a, $b, 'has', identifying: false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||..|| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToOneWithCardinalityNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToOne($a, $b, 'has', Relation::ZERO_OR_ONE, false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||..o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToMany(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToMany($a, $b, 'has'));

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
            ->addRelation(new OneToMany($a, $b, 'has', Relation::ZERO_OR_MORE));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--o{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToManyNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToMany($a, $b, 'has', identifying: false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||..|{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationOneToManyWithCardinalityNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToMany($a, $b, 'has', Relation::ZERO_OR_MORE, false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||..o{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToOne(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToOne($a, $b, 'has'));

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
            ->addRelation(new ManyToOne($a, $b, 'has', Relation::ZERO_OR_ONE));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToOneNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToOne($a, $b, 'has', identifying: false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o..|| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToOneWithCardinalityNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToOne($a, $b, 'has', Relation::ZERO_OR_ONE, false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o..o| "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToMany(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToMany($a, $b, 'has'));

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
            ->addRelation(new ManyToMany($a, $b, 'has', Relation::ONE_OR_MORE));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o--|{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToManyNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToMany($a, $b, 'has', identifying: false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o..o{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationManyToManyWithCardinalityNonIdentifying(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new ManyToMany($a, $b, 'has', Relation::ONE_OR_MORE, false));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" }o..|{ "B" : has',
            '',
        ]), (string)$diagram);
    }

    public function testRelationWithProps(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A', props: [
                new EntityProperty('foo', 'string'),
                new EntityProperty('bar', 'int'),
            ]))
            ->addEntity($b = new Entity('B', props: [
                new EntityProperty('foo', 'float'),
                new EntityProperty('bar', 'datetime'),
            ]))
            ->addRelation(new OneToOne($a, $b, 'has'));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|| "B" : has',
            '    "A" {',
            '        string foo',
            '        int bar',
            '    }',
            '    "B" {',
            '        float foo',
            '        datetime bar',
            '    }',
            '',
        ]), (string)$diagram);
    }

    public function testEntitiesNoRelationWithProps(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A', props: [
                new EntityProperty('foo', 'string'),
                new EntityProperty('bar', 'int'),
            ]))
            ->addEntity($b = new Entity('B', props: [
                new EntityProperty('foo', 'float'),
                new EntityProperty('bar', 'datetime'),
            ]));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" {',
            '        string foo',
            '        int bar',
            '    }',
            '    "B" {',
            '        float foo',
            '        datetime bar',
            '    }',
            '',
        ]), (string)$diagram);
    }

    public function testRelationWithPropsWithKeys(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A', props: [
                new EntityProperty('foo', 'string', [EntityProperty::PRIMARY_KEY]),
                new EntityProperty('bar', 'int', [EntityProperty::PRIMARY_KEY, EntityProperty::FOREIGN_KEY]),
            ]))
            ->addEntity($b = new Entity('B', props: [
                new EntityProperty('foo', 'float', [EntityProperty::PRIMARY_KEY, EntityProperty::FOREIGN_KEY, EntityProperty::UNIQUE_KEY]),
                new EntityProperty('bar', 'datetime', []),
            ]))
            ->addRelation(new OneToOne($a, $b, 'has'));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|| "B" : has',
            '    "A" {',
            '        string foo PK',
            '        int bar PK, FK',
            '    }',
            '    "B" {',
            '        float foo PK, FK, UK',
            '        datetime bar',
            '    }',
            '',
        ]), (string)$diagram);
    }

    public function testRelationWithPropsWithKeysAndComment(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A', props: [
                new EntityProperty('foo', 'string', [EntityProperty::PRIMARY_KEY], 'comment1'),
                new EntityProperty('bar', 'int', [EntityProperty::PRIMARY_KEY, EntityProperty::FOREIGN_KEY], 'comment2'),
            ]))
            ->addEntity($b = new Entity('B', props: [
                new EntityProperty('foo', 'float', [EntityProperty::PRIMARY_KEY, EntityProperty::FOREIGN_KEY, EntityProperty::UNIQUE_KEY], 'comment3'),
                new EntityProperty('bar', 'datetime', [], 'comment4'),
            ]))
            ->addRelation(new OneToOne($a, $b, 'has'));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|| "B" : has',
            '    "A" {',
            '        string foo PK "comment1"',
            '        int bar PK, FK "comment2"',
            '    }',
            '    "B" {',
            '        float foo PK, FK, UK "comment3"',
            '        datetime bar "comment4"',
            '    }',
            '',
        ]), (string)$diagram);
    }

    public function testRelationWithNoLabels(): void
    {
        $diagram = (new ERDiagram());
        $diagram
            ->addEntity($a = new Entity('A'))
            ->addEntity($b = new Entity('B'))
            ->addRelation(new OneToOne($a, $b));

        $this->dumpHtml($diagram);

        is(\implode(\PHP_EOL, [
            'erDiagram',
            '    "A" ||--|| "B" : ""',
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
