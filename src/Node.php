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

namespace JBZoo\MermaidPHP;

/**
 * Class Node
 * @package JBZoo\MermaidPHP
 */
class Node
{
    public const SQUARE            = '[%s]';
    public const ROUND             = '(%s)';
    public const CIRCLE            = '((%s))';
    public const ASYMMETRIC_SHAPE  = '>%s]';
    public const RHOMBUS           = '{%s}';
    public const HEXAGON           = '{{%s}}';
    public const PARALLELOGRAM     = '[/%s/]';
    public const PARALLELOGRAM_ALT = '[\%s\]';
    public const TRAPEZOID         = '[/%s\]';
    public const TRAPEZOID_ALT     = '[\%s/]';
    public const DATABASE          = '[(%s)]';
    public const SUBROUTINE        = '[[%s]]';
    public const STADIUM           = '([%s])';

    /**
     * @var bool
     */
    private static $safeMode = false;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $form = self::ROUND;

    /**
     * Node constructor.
     * @param string $identifier
     * @param string $title
     * @param string $form
     */
    public function __construct(string $identifier, string $title = '', string $form = self::ROUND)
    {
        $this->identifier = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title ?: $identifier);
        $this->setForm($form);
    }

    /**
     * @param bool $safeMode
     */
    public static function safeMode(bool $safeMode): void
    {
        static::$safeMode = $safeMode;
    }

    /**
     * @return bool
     */
    public static function isSafeMode(): bool
    {
        return static::$safeMode;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): Node
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?: $this->getId();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->title) {
            /* @phan-suppress-next-line PhanPluginPrintfVariableFormatString */
            return $this->identifier . sprintf((string)$this->form, Helper::escape($this->title)) . ';';
        }

        return "{$this->identifier};";
    }

    /**
     * @param string $form
     * @return $this
     */
    public function setForm(string $form): Node
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return string
     */
    public function getForm(): string
    {
        return $this->form;
    }
}
