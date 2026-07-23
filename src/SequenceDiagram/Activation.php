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
class Activation implements Statement
{
    private Participant $participant;
    private bool        $activate;

    public function __construct(Participant $participant, bool $activate)
    {
        $this->participant = $participant;
        $this->activate    = $activate;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(int $shift = 0): string
    {
        $keyword = $this->activate ? 'activate' : 'deactivate';

        return \str_repeat(' ', $shift) . "{$keyword} {$this->participant->getId()}";
    }
}
