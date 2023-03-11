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

namespace JBZoo\MermaidPHP;

class Link
{
    public const ARROW  = 1;
    public const LINE   = 2;
    public const DOTTED = 3;
    public const THICK  = 4;

    protected const TEMPLATES = [
        self::ARROW  => ['-->', '-->|%s|'],
        self::LINE   => [' --- ', '---|%s|'],
        self::DOTTED => ['-.->', '-. %s .-> '],
        self::THICK  => [' ==> ', ' == %s ==> '],
    ];

    protected int    $style = self::ARROW;
    protected string $text  = '';
    protected Node   $sourceNode;
    protected Node   $targetNode;

    public function __construct(Node $sourceNode, Node $targetNode, string $text = '', int $style = self::ARROW)
    {
        $this->sourceNode = $sourceNode;
        $this->targetNode = $targetNode;
        $this->setText($text);
        $this->setStyle($style);
    }

    public function __toString(): string
    {
        $line = self::TEMPLATES[$this->style][0];
        if ($this->text !== '') {
            $line = \sprintf(self::TEMPLATES[$this->style][1], Helper::escape($this->text));
        }

        return "{$this->sourceNode->getId()}{$line}{$this->targetNode->getId()};";
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setStyle(int $style): self
    {
        $this->style = $style;

        return $this;
    }
}
