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

namespace JBZoo\MermaidPHP\ERDiagram;

use JBZoo\MermaidPHP\Helper;

class Entity
{

    private static bool $safeMode   = false;
    protected string    $identifier = '';
    protected string    $title      = '';

    public function __construct(string $identifier, string $title = '')
    {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title === '' ? $identifier : $title);
    }

    public function __toString(): string
    {
        if ($this->title !== '') {
            // @phan-suppress-next-line PhanPluginPrintfVariableFormatString
            return Helper::escape($this->title);
        }

        return "{$this->identifier};";
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title === '' ? $this->getId() : $this->title;
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
