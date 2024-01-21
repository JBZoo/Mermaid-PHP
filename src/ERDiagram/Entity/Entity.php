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

namespace JBZoo\MermaidPHP\ERDiagram\Entity;

use JBZoo\MermaidPHP\Helper;

class Entity
{
    private static bool $safeMode   = false;
    protected string    $identifier = '';
    protected string    $title      = '';

    /** @var EntityProperty[] */
    protected array      $props = [];

    /**
     * @param EntityProperty[] $props
     */
    public function __construct(string $identifier, string $title = '', array $props = [])
    {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title === '' ? $identifier : $title);
        $this->setProps($props);
    }

    public function __toString(): string
    {
        if ($this->title !== '') {
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

    /**
     * @return EntityProperty[]
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * @param EntityProperty[] $props
     */
    public function setProps(array $props): void
    {
        $this->props = $props;
    }

    public function renderProps(): ?string
    {
        $spaces = \str_repeat(' ', 4);

        $props = $this->getProps();
        if ($props !== []) {
            $output = $this . ' {' . \PHP_EOL;

            foreach ($props as $prop) {
                /** @var EntityProperty $prop */
                $output .= $spaces . $spaces . $prop . \PHP_EOL;
            }

            return $output . $spaces . '}';
        }

        return null;
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
