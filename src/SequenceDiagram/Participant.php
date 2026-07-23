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

namespace JBZoo\MermaidPHP\SequenceDiagram;

use JBZoo\MermaidPHP\Helper;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Participant
{
    private static bool $safeMode = false;

    private string          $identifier;
    private string          $label;
    private ParticipantType $type;

    /** @var array<array{label: string, url: string}> */
    private array $links = [];

    public function __construct(
        string $identifier,
        string $label = '',
        ParticipantType $type = ParticipantType::PARTICIPANT,
    ) {
        $this->identifier = self::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->label      = $label;
        $this->type       = $type;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): ParticipantType
    {
        return $this->type;
    }

    public function getDeclaration(): string
    {
        if ($this->label !== '') {
            return "{$this->type->value} {$this->identifier} as {$this->label}";
        }

        return "{$this->type->value} {$this->identifier}";
    }

    public function link(string $label, string $url): self
    {
        $this->links[] = ['label' => $label, 'url' => $url];

        return $this;
    }

    /**
     * @return array<array{label: string, url: string}>
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public static function safeMode(bool $safeMode): void
    {
        self::$safeMode = $safeMode;
    }

    public static function isSafeMode(): bool
    {
        return self::$safeMode;
    }
}
