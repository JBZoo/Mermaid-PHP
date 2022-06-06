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
    private static bool $safeMode = false;

    /**
     * @var string
     */
    protected string $identifier = '';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $form = self::ROUND;

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
        self::$safeMode = $safeMode;
    }

    /**
     * @return bool
     */
    public static function isSafeMode(): bool
    {
        return self::$safeMode;
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
            return $this->identifier . \sprintf($this->form, Helper::escape($this->title)) . ';';
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
