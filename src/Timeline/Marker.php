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

use JBZoo\MermaidPHP\Timeline\Marker\EntityProperty;
use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Timeline\Event;

class Marker
{
    private static bool $safeMode   = false;
    protected string    $identifier = '';

    /** @var Event[]  */
    protected array      $events      = [];

    /** @param Event[] $events */
    public function __construct(string $identifier, array $events = [])
    {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setEvents($events);
    }

    public function __toString(): string
    {
        return "{$this->identifier}";
    }

    /** @return Event[] */
    public function getEvents(): array
    {
        return $this->events;
    }

    /** @param Event[]| $events */
    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public static function isSafeMode(): bool
    {
        return self::$safeMode;
    }

}
