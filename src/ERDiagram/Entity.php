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

    /** @var array<string, string>|array<empty>  */
    protected array      $classProperties      = [];

    /** @param array<string, string>|array<empty> $classProperties */
    public function __construct(string $identifier, string $title = '', array $classProperties = [])
    {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title === '' ? $identifier : $title);
        $this->setClassProperties($classProperties);
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

    /** @return array<string, string>|array<empty> */
    public function getClassProperties(): array
    {
        return $this->classProperties;
    }

    /** @param array<string, string>|array<empty> $classProperties */
    public function setClassProperties(array $classProperties): void
    {
        $this->classProperties = $classProperties;
    }

    public function renderClassProperties(): ?string
    {
        $spaces    = \str_repeat(' ', 4);

        $classProps = $this->getClassProperties();
        if (!empty($classProps)) {
            $output = $this . ' {' . \PHP_EOL;
            foreach ($classProps as $name => $type) {
                $output .= sprintf('%s%s %s', $spaces . $spaces, $type, $name) . \PHP_EOL;
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
