<?php

/**
 * JBZoo Toolbox - Mermaid-PHP
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Mermaid-PHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Mermaid-PHP
 */

declare(strict_types=1);

namespace JBZoo\MermaidPHP;

/**
 * Class Link
 * @package JBZoo\MermaidPHP
 */
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

    /**
     * @var Node
     */
    protected $sourceNode;

    /**
     * @var Node
     */
    protected $targetNode;

    /**
     * @var int
     */
    protected $style = self::ARROW;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @param Node   $sourceNode
     * @param Node   $targetNode
     * @param string $text
     * @param int    $style
     */
    public function __construct(Node $sourceNode, Node $targetNode, string $text = '', int $style = self::ARROW)
    {
        $this->sourceNode = $sourceNode;
        $this->targetNode = $targetNode;
        $this->setText($text);
        $this->setStyle($style);
    }

    /**
     * @param string $text
     * @return Link
     */
    public function setText(string $text): Link
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param int $style
     * @return Link
     */
    public function setStyle(int $style): Link
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $line = self::TEMPLATES[$this->style][0];
        if ($this->text) {
            $line = \sprintf(self::TEMPLATES[$this->style][1], Helper::escape($this->text));
        }

        return "{$this->sourceNode->getId()}{$line}{$this->targetNode->getId()};";
    }
}
