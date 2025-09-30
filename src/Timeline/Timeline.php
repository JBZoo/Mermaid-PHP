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

namespace JBZoo\MermaidPHP\Timeline;

use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Render;
use JBZoo\MermaidPHP\Timeline\Exception\SectionHasNoTitleException;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Timeline
{
    private const RENDER_SHIFT = 4;

    /** @var Timeline[] */
    private array $sections = [];

    /** @var Marker[] */
    private array $markers = [];

    /** @var mixed[] */
    private array $params = [
        'title' => '',
    ];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @throws SectionHasNoTitleException
     */
    public function addSection(self $section): self
    {
        if ($section->getParams()['title'] === '') {
            throw new SectionHasNoTitleException();
        }
        $this->sections[] = $section;

        return $this;
    }

    public function addMarker(Marker $marker): self
    {
        $this->markers[$marker->getId()] = $marker;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(bool $isMainTimeline = true, int $shift = 0): string
    {
        $spaces    = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        $params = $this->getParams();

        if ($isMainTimeline) {
            $result = ['timeline'];

            if (isset($params['title']) && $params['title'] !== '') {
                $result[] = $spacesSub . 'title ' . $params['title'];
            }
        } else {
            $result = ["{$spaces}section " . Helper::escape((string)$params['title'])];
        }

        foreach ($this->sections as $section) {
            $result[] = $section->render(false, $shift + 4);
        }

        if (\count($this->markers) > 0) {
            $tmp = [];

            foreach ($this->markers as $marker) {
                $tmpMarker   = [];
                $tmpMarker[] = $spacesSub . $marker->__toString();

                $events = $marker->getEvents();
                if ($events !== []) {
                    foreach ($events as $event) {
                        $tmpMarker[] = $event;
                    }
                }
                $tmp[] = \implode(' : ', $tmpMarker);
            }

            $result = \array_merge($result, $tmp);
        }

        if ($isMainTimeline) {
            $result[] = '';
        }

        return \implode(\PHP_EOL, $result);
    }

    /**
     * @param array<string> $params
     */
    public function renderHtml(array $params = []): string
    {
        return Render::html($this, $params);
    }

    public function getLiveEditorUrl(): string
    {
        return Helper::getLiveEditorUrl($this);
    }
}
