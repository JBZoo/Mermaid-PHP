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

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Box
{
    private string  $label;
    private ?string $color;

    /** @var Participant[] */
    private array $participants = [];

    public function __construct(string $label, ?string $color = null)
    {
        $this->label = $label;
        $this->color = $color;
    }

    public function addParticipant(Participant $participant): self
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * @return Participant[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function render(int $shift = 0): string
    {
        $spaces = \str_repeat(' ', $shift);
        $head   = $this->color !== null && $this->color !== ''
            ? "box {$this->color} {$this->label}"
            : "box {$this->label}";

        $lines = [$spaces . $head];

        foreach ($this->participants as $participant) {
            $lines[] = \str_repeat(' ', $shift + 4) . $participant->getDeclaration();
        }
        $lines[] = $spaces . 'end';

        return \implode(\PHP_EOL, $lines);
    }
}
