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

namespace JBZoo\MermaidPHP\SequenceDiagram\Block;

use JBZoo\MermaidPHP\SequenceDiagram\Statement;

abstract class Block implements Statement
{
    protected string $keyword;

    /** @var array<int, array{divider: ?string, label: ?string, statements: Statement[]}> */
    protected array $sections;

    public function __construct(string $keyword, ?string $label = null)
    {
        $this->keyword  = $keyword;
        $this->sections = [['divider' => null, 'label' => $label, 'statements' => []]];
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function add(Statement $statement): static
    {
        $lastIndex                                  = \count($this->sections) - 1;
        $this->sections[$lastIndex]['statements'][] = $statement;

        return $this;
    }

    public function render(int $shift = 0): string
    {
        $spaces = \str_repeat(' ', $shift);
        $lines  = [];

        foreach ($this->sections as $index => $section) {
            $keyword = $index === 0 ? $this->keyword : (string)$section['divider'];
            $label   = $section['label'];
            $lines[] = $spaces . ($label !== null && $label !== '' ? "{$keyword} {$label}" : $keyword);

            foreach ($section['statements'] as $statement) {
                $lines[] = $statement->render($shift + 4);
            }
        }

        $lines[] = $spaces . 'end';

        return \implode(\PHP_EOL, $lines);
    }

    protected function addSection(string $divider, ?string $label): static
    {
        $this->sections[] = ['divider' => $divider, 'label' => $label, 'statements' => []];

        return $this;
    }
}
