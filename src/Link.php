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
    protected $type = self::ARROW;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var ?string
     */
    protected $style = '';

    /**
     * @var int|null
     */
    protected $index = null;

    /**
     * @param Node   $sourceNode
     * @param Node   $targetNode
     * @param string $text
     * @param int    $type
     * @param string|null $style
     */
    public function __construct(Node $sourceNode, Node $targetNode, string $text = '', int $type = self::ARROW, ?string $style = null)
    {
        $this->sourceNode = $sourceNode;
        $this->targetNode = $targetNode;
        $this->style = $style;
        $this->setText($text);
        $this->setType($type);
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
     * @param int $type
     * @return Link
     */
    public function setType(int $type): Link
    {
        $this->type = $type;
        return $this;
    }

    /**
     * The link is assigned an index at render time, which is used in getStyle()
     * @param int $index
     * @return Link
     */
    public function setIndex(int $index): Link
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $line = self::TEMPLATES[$this->type][0];
        if ($this->text) {
            $line = sprintf(self::TEMPLATES[$this->type][1], Helper::escape($this->text));
        }

        return "{$this->sourceNode->getId()}{$line}{$this->targetNode->getId()};";
    }

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        if (is_null($this->index) || is_null($this->style)) {
            return null;
        }
        return "linkStyle $this->index $this->style";
    }
}
