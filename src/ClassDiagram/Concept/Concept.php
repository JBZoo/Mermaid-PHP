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

namespace JBZoo\MermaidPHP\ClassDiagram\Concept;

use JBZoo\MermaidPHP\Helper;

class Concept
{
    private static bool $safeMode = false;
    protected string    $identifier;

    /** @var Attribute[] */
    protected array     $attributes;

    /** @var Method[] */
    protected array     $methods;

    protected ?string   $annotation;

    /**
     * @param Attribute[] $attributes
     * @param Method[]    $methods
     */
    public function __construct(
        string $identifier,
        array $attributes = [],
        array $methods = [],
        ?string $annotation = null,
    ) {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->attributes = $attributes;
        $this->methods    = $methods;
        $this->annotation = $annotation;
    }

    public function __toString(): string
    {
        $result = $this->getLinesToRender();

        return \implode(\PHP_EOL, $result);
    }

    public function getLinesToRender(): array
    {
        $spaces = \str_repeat(' ', 4);

        $result = [];

        $result[] = \sprintf('class %s {', $this->identifier);

        if ($this->annotation !== null) {
            $result[] = $spaces . \sprintf('<<%s>>', $this->annotation);
        }

        foreach ($this->attributes as $attribute) {
            $result[] = $spaces . $attribute;
        }

        foreach ($this->methods as $method) {
            $result[] = $spaces . $method;
        }

        $result[] = '}';

        return $result;
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
