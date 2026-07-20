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

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Node
{
    public const string SQUARE            = '[%s]';
    public const string ROUND             = '(%s)';
    public const string CIRCLE            = '((%s))';
    public const string ASYMMETRIC_SHAPE  = '>%s]';
    public const string RHOMBUS           = '{%s}';
    public const string HEXAGON           = '{{%s}}';
    public const string PARALLELOGRAM     = '[/%s/]';
    public const string PARALLELOGRAM_ALT = '[\%s\]';
    public const string TRAPEZOID         = '[/%s\]';
    public const string TRAPEZOID_ALT     = '[\%s/]';
    public const string DATABASE          = '[(%s)]';
    public const string SUBROUTINE        = '[[%s]]';
    public const string STADIUM           = '([%s])';

    private static bool $safeMode = false;
    private string    $identifier = '';
    private string    $title      = '';
    private string    $form       = self::ROUND;

    public function __construct(string $identifier, string $title = '', string $form = self::ROUND)
    {
        $this->identifier = self::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title === '' ? $identifier : $title);
        $this->setForm($form);
    }

    public function __toString(): string
    {
        if ($this->title !== '') {
            // @phan-suppress-next-line PhanPluginPrintfVariableFormatString
            return $this->identifier . \sprintf($this->form, Helper::escape($this->title)) . ';';
        }

        return "{$this->identifier};";
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title === '' ? $this->getId() : $this->title;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public function setForm(string $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getForm(): string
    {
        return $this->form;
    }

    public static function safeMode(bool $safeMode): void
    {
        self::$safeMode = $safeMode;
    }

    public static function isSafeMode(): bool
    {
        return self::$safeMode;
    }
}
