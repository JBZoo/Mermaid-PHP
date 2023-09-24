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

use JBZoo\MermaidPHP\Timeline\Marker;
use JBZoo\MermaidPHP\Timeline\Event;
use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Render;

class Timeline
{
    private const RENDER_SHIFT = 4;

    /** @var Marker[] */
    protected array $markers = [];

    /** @var mixed[] */
    protected array $params = [
        'abc_order' => false,
        'title'     => '',
    ];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
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
    public function render(int $shift = 0): string
    {
        $spaces    = \str_repeat(' ', $shift);
        $spacesSub = \str_repeat(' ', $shift + self::RENDER_SHIFT);

        $result = [];
        $params = $this->getParams();

        $result[] = "timeline";

        if (!empty($params['title'])) {
            $result[] = $spacesSub . "title " . $params['title'];
        }

        if (\count($this->markers) > 0) {
            $tmp = [];

            foreach ($this->markers as $marker) {
                $tmpMarker = [];
                $tmpMarker[] = $spacesSub . $marker;

                $events = $marker->getEvents();
                if (!empty($events)) {
                    foreach ($events as $event) {
                        $tmpMarker[] = $event;
                    }
                }
                $tmp[] = implode(' : ', $tmpMarker);
            }


            if ($this->params['abc_order'] === true) {
                \sort($tmp);
            }

            $result = \array_merge($result, $tmp);
        }

        $result[] = '';
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

    public function __toString(): string
    {
        return $this->render();
    }

}
