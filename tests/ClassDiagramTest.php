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

use JBZoo\MermaidPHP\ClassDiagram\ClassDiagram;
use JBZoo\MermaidPHP\ClassDiagram\Concept\Argument;
use JBZoo\MermaidPHP\ClassDiagram\Concept\Attribute;
use JBZoo\MermaidPHP\ClassDiagram\Concept\Concept;
use JBZoo\MermaidPHP\ClassDiagram\Concept\Method;
use JBZoo\MermaidPHP\ClassDiagram\Concept\Visibility;
use JBZoo\MermaidPHP\ClassDiagram\ConceptNamespace\ConceptNamespace;
use JBZoo\MermaidPHP\ClassDiagram\Relationship\Cardinality;
use JBZoo\MermaidPHP\ClassDiagram\Relationship\Relationship;
use JBZoo\MermaidPHP\ClassDiagram\Relationship\RelationType;
use JBZoo\MermaidPHP\Direction;

class ClassDiagramTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBasicClassDiagram(): void
    {
        $diagram = new ClassDiagram();
        $diagram->addClass(new Concept('A', [], [], 'interface'));
        $diagram->addClass(
            class: new Concept(
                'BankAccount',
                attributes: [
                    new Attribute('owner', 'String'),
                    new Attribute('balance', 'BigDecimal', Visibility::PUBLIC),
                ],
                methods: [
                    new Method(
                        name: 'deposit',
                        arguments: [new Argument('amount', 'int')],
                        returnType: 'bool',
                        isStatic: true,
                    ),
                    new Method(
                        name: 'withdrawal',
                        arguments: [new Argument('amount')],
                        visibility: Visibility::PUBLIC,
                        isAbstract: true,
                    ),
                ],
            ),
        );

        is(\implode(\PHP_EOL, [
            'classDiagram',
            'class A {',
            '    <<interface>>',
            '}',
            'class BankAccount {',
            '    String owner',
            '    +BigDecimal balance',
            '    deposit(int amount) bool$',
            '    +withdrawal(amount)*',
            '}',
            '',
        ]), (string)$diagram);
    }

    public function testDiagramWithTitle(): void
    {
        $diagram = new ClassDiagram();
        $diagram->setTitle('Animal example');

        is(\implode(\PHP_EOL, [
            '---',
            'title: Animal example',
            '---',
            'classDiagram',
            '',
        ]), (string)$diagram);
    }

    public function testDiagramWithDirection(): void
    {
        $diagram = new ClassDiagram();
        $diagram->setDirection(Direction::BOTTOM_TO_TOP);

        is(\implode(\PHP_EOL, [
            'classDiagram',
            'direction BT',
            '',
        ]), (string)$diagram);
    }

    public function testClassWithNoMembers(): void
    {
        $class = new Concept('A');

        is(\implode(\PHP_EOL, [
            'class A {',
            '}',
        ]), (string)$class);
    }

    public function testClassWithAttributes(): void
    {
        $class = new Concept('Animal', [
            new Attribute('age', 'int', Visibility::PUBLIC),
            new Attribute('gender', 'String', Visibility::PRIVATE),
        ]);

        is(\implode(\PHP_EOL, [
            'class Animal {',
            '    +int age',
            '    -String gender',
            '}',
        ]), (string)$class);
    }

    public function testRelationship(): void
    {
        $relationship = new Relationship(
            classA: new Concept('A'),
            classB: new Concept('B'),
            relationType: RelationType::INHERITANCE,
        );

        is('A --|> B', (string)$relationship);
    }

    public function testRelationshipWithLabel(): void
    {
        $relationship = new Relationship(
            classA: new Concept('A'),
            classB: new Concept('B'),
            relationType: RelationType::COMPOSITION,
            label: 'composition',
        );

        is('A *-- B : composition', (string)$relationship);
    }

    public function testRelationshipWithCardinality(): void
    {
        $relationship = new Relationship(
            classA: new Concept('A'),
            classB: new Concept('B'),
            relationType: RelationType::AGGREGATION,
            label: 'composition',
            cardinalityA: Cardinality::ONE,
            cardinalityB: 'many',
        );

        is('A "1" o-- "many" B : composition', (string)$relationship);
    }

    public function testRelationshipWithInverse(): void
    {
        $relationship = new Relationship(
            classA: new Concept('A'),
            classB: new Concept('B'),
            relationType: RelationType::AGGREGATION,
            inverseRelationType: RelationType::AGGREGATION,
        );

        is('A o--o B', (string)$relationship);
    }

    public function testNamespace(): void
    {
        $namespace = new ConceptNamespace('BaseShapes', [
            new Concept('Triangle'),
            new Concept('Rectangle', [
                new Attribute('width', 'double'),
                new Attribute('height', 'double'),
            ]),
        ]);

        is(\implode(\PHP_EOL, [
            'namespace BaseShapes {',
            '    class Triangle {',
            '    }',
            '    class Rectangle {',
            '        double width',
            '        double height',
            '    }',
            '}',
        ]), (string)$namespace);
    }
}
