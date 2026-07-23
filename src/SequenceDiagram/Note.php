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

use JBZoo\MermaidPHP\Exception;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Note implements Statement
{
    private string       $text;
    private NotePosition $position;
    private Participant  $participant;
    private ?Participant $secondParticipant;

    public function __construct(
        string $text,
        NotePosition $position,
        Participant $participant,
        ?Participant $secondParticipant = null,
    ) {
        if ($secondParticipant !== null && $position !== NotePosition::OVER) {
            throw new Exception('A note spanning two participants must use NotePosition::OVER.');
        }

        $this->text              = $text;
        $this->position          = $position;
        $this->participant       = $participant;
        $this->secondParticipant = $secondParticipant;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(int $shift = 0): string
    {
        $targets = $this->participant->getId();
        if ($this->secondParticipant !== null) {
            $targets .= ',' . $this->secondParticipant->getId();
        }

        return \str_repeat(' ', $shift)
            . "Note {$this->position->value} {$targets}: " . \trim($this->text);
    }
}
