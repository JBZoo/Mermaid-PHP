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

final class EntityProperty implements \Stringable
{
    public const PRIMARY_KEY = 'PK';
    public const FOREIGN_KEY = 'FK';
    public const UNIQUE_KEY  = 'UK';

    /**
     * @param string[] $keys
     */
    public function __construct(
        private string $name,
        private string $type,
        private array $keys = [],
        private string $comment = '',
    ) {
    }

    public function __toString(): string
    {
        $output   = [];
        $output[] = $this->getType();
        $output[] = $this->getName();

        if ($this->getKeys() !== []) {
            $output[] = \implode(', ', $this->getKeys());
        }

        if ($this->getComment() !== '') {
            $output[] = '"' . $this->getComment() . '"';
        }

        return \implode(' ', $output);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param string[] $keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
