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

use JBZoo\MermaidPHP\Timeline\Event;
use JBZoo\MermaidPHP\Timeline\Marker;
use JBZoo\MermaidPHP\Timeline\Timeline;

final class TimelineTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBasicTimeline(): void
    {
        $timeline = (new Timeline(['title' => 'History of Social Media Platform']))
            ->addMarker(new Marker('2002', [
                new Event('Linkedin'),
            ]))
            ->addMarker(new Marker('2004', [
                new Event('Facebook'),
                new Event('Google'),
            ]))
            ->addMarker(new Marker('2005', [
                new Event('Youtube'),
            ]))
            ->addMarker(new Marker('2006', [
                new Event('Twitter'),
            ]));

        $this->dumpHtml($timeline);

        is(\implode(\PHP_EOL, [
            'timeline',
            '    title History of Social Media Platform',
            '    2002 : Linkedin',
            '    2004 : Facebook : Google',
            '    2005 : Youtube',
            '    2006 : Twitter',
            '',
        ]), (string)$timeline);
    }

    public function testTimelineNoTitle(): void
    {
        $timeline = (new Timeline())
            ->addMarker(new Marker('2002', [
                new Event('Linkedin'),
            ]))
            ->addMarker(new Marker('2004', [
                new Event('Facebook'),
                new Event('Google'),
            ]))
            ->addMarker(new Marker('2005', [
                new Event('Youtube'),
            ]))
            ->addMarker(new Marker('2006', [
                new Event('Twitter'),
            ]));

        $this->dumpHtml($timeline);

        is(\implode(\PHP_EOL, [
            'timeline',
            '    2002 : Linkedin',
            '    2004 : Facebook : Google',
            '    2005 : Youtube',
            '    2006 : Twitter',
            '',
        ]), (string)$timeline);
    }

    public function testTimelineOneSubSection(): void
    {
        $timeline = (new Timeline(['title' => 'History of Social Media Platform']))
            ->addSection(
                (new Timeline(['title' => 'Subsection 1']))
                    ->addMarker(new Marker('2002', [
                        new Event('Linkedin'),
                    ]))
                    ->addMarker(new Marker('2004', [
                        new Event('Facebook'),
                        new Event('Google'),
                    ]))
                    ->addMarker(new Marker('2005', [
                        new Event('Youtube'),
                    ]))
                    ->addMarker(new Marker('2006', [
                        new Event('Twitter'),
                    ])),
            );

        $this->dumpHtml($timeline);

        is(\implode(\PHP_EOL, [
            'timeline',
            '    title History of Social Media Platform',
            '    section "Subsection 1"',
            '        2002 : Linkedin',
            '        2004 : Facebook : Google',
            '        2005 : Youtube',
            '        2006 : Twitter',
            '',
        ]), (string)$timeline);
    }

    public function testTimelineTwoSubSection(): void
    {
        $timeline = (new Timeline(['title' => 'History of Social Media Platform']))
            ->addSection(
                (new Timeline(['title' => 'Subsection 1']))
                    ->addMarker(new Marker('2002', [
                        new Event('Linkedin'),
                    ])),
            )
            ->addSection(
                (new Timeline(['title' => 'Subsection 2']))
                    ->addMarker(new Marker('2004', [
                        new Event('Facebook'),
                        new Event('Google'),
                    ]))
                    ->addMarker(new Marker('2005', [
                        new Event('Youtube'),
                    ]))
                    ->addMarker(new Marker('2006', [
                        new Event('Twitter'),
                    ])),
            );

        $this->dumpHtml($timeline);

        is(\implode(\PHP_EOL, [
            'timeline',
            '    title History of Social Media Platform',
            '    section "Subsection 1"',
            '        2002 : Linkedin',
            '    section "Subsection 2"',
            '        2004 : Facebook : Google',
            '        2005 : Youtube',
            '        2006 : Twitter',
            '',
        ]), (string)$timeline);
    }

    protected function dumpHtml(Timeline $timeline): void
    {
        \file_put_contents(
            PROJECT_ROOT . '/build/index.html',
            $timeline->renderHtml(['debug' => true, 'title' => $this->getName()]),
        );
    }
}
