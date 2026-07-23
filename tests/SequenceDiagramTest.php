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

use JBZoo\MermaidPHP\Exception;
use JBZoo\MermaidPHP\SequenceDiagram\Activation;
use JBZoo\MermaidPHP\SequenceDiagram\ArrowType;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Alt;
use JBZoo\MermaidPHP\SequenceDiagram\Block\BreakBlock;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Critical;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Loop;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Opt;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Par;
use JBZoo\MermaidPHP\SequenceDiagram\Block\Rect;
use JBZoo\MermaidPHP\SequenceDiagram\Box;
use JBZoo\MermaidPHP\SequenceDiagram\Comment;
use JBZoo\MermaidPHP\SequenceDiagram\Lifecycle;
use JBZoo\MermaidPHP\SequenceDiagram\Message;
use JBZoo\MermaidPHP\SequenceDiagram\Note;
use JBZoo\MermaidPHP\SequenceDiagram\NotePosition;
use JBZoo\MermaidPHP\SequenceDiagram\Participant;
use JBZoo\MermaidPHP\SequenceDiagram\ParticipantType;
use JBZoo\MermaidPHP\SequenceDiagram\SequenceDiagram;

final class SequenceDiagramTest extends PHPUnit
{
    public function testParticipantDeclaration(): void
    {
        Participant::safeMode(false);

        isSame('alice', (new Participant('alice'))->getId());
        isSame('participant alice', (new Participant('alice'))->getDeclaration());
        isSame('participant alice as Alice', (new Participant('alice', 'Alice'))->getDeclaration());
        isSame(
            'actor bob as Bob',
            (new Participant('bob', 'Bob', ParticipantType::ACTOR))->getDeclaration(),
        );
        isSame('alice', (string)(new Participant('alice', 'Alice')));
    }

    public function testParticipantLinks(): void
    {
        Participant::safeMode(false);

        $alice = (new Participant('alice', 'Alice'))
            ->link('Dashboard', 'https://example.com/dash')
            ->link('Wiki', 'https://example.com/wiki');

        isSame([
            ['label' => 'Dashboard', 'url' => 'https://example.com/dash'],
            ['label' => 'Wiki', 'url' => 'https://example.com/wiki'],
        ], $alice->getLinks());
    }

    public function testParticipantSafeMode(): void
    {
        Participant::safeMode(true);
        isSame(\md5('alice'), (new Participant('alice'))->getId());
        Participant::safeMode(false);
        isSame('alice', (new Participant('alice'))->getId());
    }

    public function testMessageArrowTypes(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        isSame('a->>b: Hi', (string)(new Message($a, $b, 'Hi')));
        isSame('a->b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::SOLID)));
        isSame('a-->b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::DOTTED)));
        isSame('a-->>b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::DOTTED_ARROW)));
        isSame('a<<->>b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::BI_SOLID)));
        isSame('a<<-->>b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::BI_DOTTED)));
        isSame('a-xb: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::CROSS)));
        isSame('a--xb: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::DOTTED_CROSS)));
        isSame('a-)b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::ASYNC)));
        isSame('a--)b: Hi', (string)(new Message($a, $b, 'Hi', ArrowType::DOTTED_ASYNC)));

        // No text -> no colon.
        isSame('a->>b', (string)(new Message($a, $b)));
    }

    public function testMessageActivation(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        isSame('a->>+b: open', (string)(new Message($a, $b, 'open', ArrowType::SOLID_ARROW, true)));
        isSame('b-->>-a: close', (string)(new Message($b, $a, 'close', ArrowType::DOTTED_ARROW, false, true)));
    }

    public function testMessageRejectsBothActivationFlags(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('activate the target and deactivate the source');
        new Message($a, $b, 'x', ArrowType::SOLID_ARROW, true, true);
    }

    public function testNoteRendering(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        isSame('Note right of a: hi', (string)(new Note('hi', NotePosition::RIGHT_OF, $a)));
        isSame('Note left of a: hi', (string)(new Note('hi', NotePosition::LEFT_OF, $a)));
        isSame('Note over a: hi', (string)(new Note('hi', NotePosition::OVER, $a)));
        isSame('Note over a,b: hi', (string)(new Note('hi', NotePosition::OVER, $a, $b)));
    }

    public function testNoteRejectsTwoParticipantsWhenNotOver(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('must use NotePosition::OVER');
        new Note('hi', NotePosition::RIGHT_OF, $a, $b);
    }

    public function testActivationStatements(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');

        isSame('activate a', (string)(new Activation($a, true)));
        isSame('deactivate a', (string)(new Activation($a, false)));
    }

    public function testLifecycleStatements(): void
    {
        Participant::safeMode(false);
        $carl = new Participant('carl', 'Carl');
        $don  = new Participant('d', 'Donald', ParticipantType::ACTOR);

        isSame('create participant carl as Carl', (string)(new Lifecycle($carl, true)));
        isSame('create actor d as Donald', (string)(new Lifecycle($don, true)));
        isSame('destroy carl', (string)(new Lifecycle($carl, false)));
    }

    public function testCommentStatement(): void
    {
        isSame('%% a note', (string)(new Comment('a note')));
    }

    public function testBoxRendering(): void
    {
        Participant::safeMode(false);

        $box = (new Box('Group', 'lightgreen'))
            ->addParticipant(new Participant('a', 'Alice'))
            ->addParticipant(new Participant('b'));

        isSame(\implode(\PHP_EOL, [
            'box lightgreen Group',
            '    participant a as Alice',
            '    participant b',
            'end',
        ]), $box->render());

        $noColor = (new Box('Group'))->addParticipant(new Participant('a'));
        isSame(\implode(\PHP_EOL, [
            'box Group',
            '    participant a',
            'end',
        ]), $noColor->render());
    }

    public function testSingleSectionBlocks(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $loop = (new Loop('every minute'))
            ->add(new Message($a, $b, 'ping'));
        isSame(\implode(\PHP_EOL, [
            'loop every minute',
            '    a->>b: ping',
            'end',
        ]), $loop->render());

        $rect = (new Rect('rgb(200, 255, 200)'))
            ->add(new Message($a, $b, 'hi'));
        isSame(\implode(\PHP_EOL, [
            'rect rgb(200, 255, 200)',
            '    a->>b: hi',
            'end',
        ]), $rect->render());

        $break = (new BreakBlock('when out of stock'))
            ->add(new Message($a, $b, 'abort'));
        isSame(\implode(\PHP_EOL, [
            'break when out of stock',
            '    a->>b: abort',
            'end',
        ]), $break->render());

        $opt = (new Opt('if needed'))
            ->add(new Message($a, $b, 'maybe'));
        isSame(\implode(\PHP_EOL, [
            'opt if needed',
            '    a->>b: maybe',
            'end',
        ]), $opt->render());
    }

    public function testNestedBlocks(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $outer = (new Loop('outer'))
            ->add(
                (new Opt('inner'))
                    ->add(new Message($a, $b, 'deep')),
            );

        isSame(\implode(\PHP_EOL, [
            'loop outer',
            '    opt inner',
            '        a->>b: deep',
            '    end',
            'end',
        ]), $outer->render());
    }

    public function testAltBlock(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $alt = (new Alt('is sick'))
            ->add(new Message($b, $a, 'Not so good'))
            ->addElse('is well')
            ->add(new Message($b, $a, 'Fresh'));

        isSame(\implode(\PHP_EOL, [
            'alt is sick',
            '    b->>a: Not so good',
            'else is well',
            '    b->>a: Fresh',
            'end',
        ]), $alt->render());
    }

    public function testParAndCriticalBlocks(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $par = (new Par('to Bob'))
            ->add(new Message($a, $b, 'm1'))
            ->addAnd('to Carl')
            ->add(new Message($a, $b, 'm2'));
        isSame(\implode(\PHP_EOL, [
            'par to Bob',
            '    a->>b: m1',
            'and to Carl',
            '    a->>b: m2',
            'end',
        ]), $par->render());

        $critical = (new Critical('Connect to DB'))
            ->add(new Message($a, $b, 'connect'))
            ->addOption('Timeout')
            ->add(new Message($a, $b, 'log'));
        isSame(\implode(\PHP_EOL, [
            'critical Connect to DB',
            '    a->>b: connect',
            'option Timeout',
            '    a->>b: log',
            'end',
        ]), $critical->render());
    }

    public function testFullDiagramRendering(): void
    {
        Participant::safeMode(false);
        $alice = new Participant('alice', 'Alice');
        $john  = new Participant('john', 'John', ParticipantType::ACTOR);

        $diagram = (new SequenceDiagram(['title' => 'Demo', 'autonumber' => true]))
            ->addParticipant($alice)
            ->addParticipant($john)
            ->addMessage(new Message($alice, $john, 'Hello', ArrowType::SOLID_ARROW, true))
            ->addMessage(new Message($john, $alice, 'Hi', ArrowType::DOTTED_ARROW, false, true))
            ->addNote(new Note('greeting', NotePosition::OVER, $alice, $john));

        isSame(\implode(\PHP_EOL, [
            '---',
            'title: Demo',
            '---',
            'sequenceDiagram',
            '    autonumber',
            '    participant alice as Alice',
            '    actor john as John',
            '    alice->>+john: Hello',
            '    john-->>-alice: Hi',
            '    Note over alice,john: greeting',
            '',
        ]), (string)$diagram);
    }

    public function testDiagramNoTitleNoAutonumber(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a');
        $b = new Participant('b');

        $diagram = (new SequenceDiagram())
            ->addParticipant($a)
            ->addParticipant($b)
            ->addMessageByIds('a', 'b', 'hi');

        isSame(\implode(\PHP_EOL, [
            'sequenceDiagram',
            '    participant a',
            '    participant b',
            '    a->>b: hi',
            '',
        ]), (string)$diagram);
    }

    public function testBoxAndLinksInHeader(): void
    {
        Participant::safeMode(false);
        $a = (new Participant('a', 'Alice'))->link('Dashboard', 'https://x/a');

        $diagram = (new SequenceDiagram())
            ->addBox((new Box('Team'))->addParticipant($a))
            ->addMessage(new Message($a, $a, 'self'));

        isSame(\implode(\PHP_EOL, [
            'sequenceDiagram',
            '    box Team',
            '        participant a as Alice',
            '    end',
            '    link a: Dashboard @ https://x/a',
            '    a->>a: self',
            '',
        ]), (string)$diagram);
    }

    public function testAddMessageByIdsUnknownSource(): void
    {
        Participant::safeMode(false);
        $diagram = (new SequenceDiagram())->addParticipant(new Participant('b'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source participant id="a" not found');
        $diagram->addMessageByIds('a', 'b');
    }

    public function testAddMessageByIdsUnknownTarget(): void
    {
        Participant::safeMode(false);
        $diagram = (new SequenceDiagram())->addParticipant(new Participant('a'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Target participant id="b" not found');
        $diagram->addMessageByIds('a', 'b');
    }

    public function testRenderHtmlAndLiveEditorUrl(): void
    {
        Participant::safeMode(false);
        $a = new Participant('a', 'Alice');
        $b = new Participant('b', 'Bob');

        $diagram = (new SequenceDiagram(['title' => 'Demo']))
            ->addParticipant($a)
            ->addParticipant($b)
            ->addMessage(new Message($a, $b, 'Hello'));

        $this->dumpHtml($diagram);

        $html = $diagram->renderHtml(['title' => 'Sequence']);
        isContain('sequenceDiagram', $html);
        isContain('a->>b: Hello', $html);
        isContain('<div class="mermaid"', $html);

        isContain('https://mermaid-js.github.io/mermaid-live-editor/#/edit/', $diagram->getLiveEditorUrl());

        // Debug block relies on getParams().
        isSame(['title' => 'Demo', 'autonumber' => false], $diagram->getParams());
    }

    public function testKitchenSink(): void
    {
        Participant::safeMode(false);
        $alice = new Participant('alice', 'Alice');
        $bob   = new Participant('bob', 'Bob');
        $carl  = new Participant('carl', 'Carl');

        $diagram = (new SequenceDiagram())
            ->addParticipant($alice)
            ->addParticipant($bob)
            ->comment('a demo')
            ->addMessage(new Message($alice, $bob, 'Hello', ArrowType::SOLID_ARROW, true))
            ->addStatement(
                (new Alt('ok'))
                    ->add(new Message($bob, $alice, 'Yes', ArrowType::DOTTED_ARROW))
                    ->addElse('no')
                    ->add(new Message($bob, $alice, 'No', ArrowType::DOTTED_ARROW)),
            )
            ->deactivate($bob)
            ->create($carl)
            ->addMessage(new Message($alice, $carl, 'Hi Carl'))
            ->destroy($carl);

        isSame(\implode(\PHP_EOL, [
            'sequenceDiagram',
            '    participant alice as Alice',
            '    participant bob as Bob',
            '    %% a demo',
            '    alice->>+bob: Hello',
            '    alt ok',
            '        bob-->>alice: Yes',
            '    else no',
            '        bob-->>alice: No',
            '    end',
            '    deactivate bob',
            '    create participant carl as Carl',
            '    alice->>carl: Hi Carl',
            '    destroy carl',
            '',
        ]), (string)$diagram);
    }

    protected function dumpHtml(SequenceDiagram $diagram): void
    {
        \file_put_contents(
            PROJECT_ROOT . '/build/index.html',
            $diagram->renderHtml(['debug' => true, 'title' => $this->name()]),
        );
    }
}
