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
class Lifecycle implements Statement
{
    private Participant $participant;
    private bool        $create;

    public function __construct(Participant $participant, bool $create)
    {
        $this->participant = $participant;
        $this->create      = $create;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(int $shift = 0): string
    {
        $spaces = \str_repeat(' ', $shift);
        if ($this->create) {
            return $spaces . 'create ' . $this->participant->getDeclaration();
        }

        return $spaces . 'destroy ' . $this->participant->getId();
    }
}
