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
class Message implements Statement
{
    private Participant $source;
    private Participant $target;
    private string      $text;
    private ArrowType   $arrow;
    private bool        $activateTarget;
    private bool        $deactivateSource;

    public function __construct(
        Participant $source,
        Participant $target,
        string $text = '',
        ArrowType $arrow = ArrowType::SOLID_ARROW,
        bool $activateTarget = false,
        bool $deactivateSource = false,
    ) {
        if ($activateTarget && $deactivateSource) {
            throw new Exception('A message cannot activate the target and deactivate the source at once.');
        }

        $this->source           = $source;
        $this->target           = $target;
        $this->text             = $text;
        $this->arrow            = $arrow;
        $this->activateTarget   = $activateTarget;
        $this->deactivateSource = $deactivateSource;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(int $shift = 0): string
    {
        $sign = '';
        if ($this->activateTarget) {
            $sign = '+';
        } elseif ($this->deactivateSource) {
            $sign = '-';
        }

        $text   = \trim($this->text);
        $suffix = $text !== '' ? ": {$text}" : '';

        return \str_repeat(' ', $shift)
            . "{$this->source->getId()}{$this->arrow->value}{$sign}{$this->target->getId()}{$suffix}";
    }
}
