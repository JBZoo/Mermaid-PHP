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

class Event
{
    private static bool $safeMode = false;
    private string    $identifier = '';

    public function __construct(string $identifier)
    {
        $this->identifier = self::isSafeMode() ? Helper::getId($identifier) : $identifier;
    }

    public function __toString(): string
    {
        return "{$this->identifier}";
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
