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

    public const string TARGET_BLANK  = '_blank';
    public const string TARGET_SELF   = '_self';
    public const string TARGET_PARENT = '_parent';
    public const string TARGET_TOP    = '_top';

    private static bool $safeMode = false;
    private string    $identifier = '';
    private string    $title      = '';
    private string    $form       = self::ROUND;
    private ?string   $url        = null;
    private ?string   $tooltip    = null;
    private ?string   $target     = null;

    public function __construct(
        string $identifier,
        string $title = '',
        string $form = self::ROUND,
        ?string $url = null,
    ) {
        $this->identifier = self::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->setTitle($title === '' ? $identifier : $title);
        $this->setForm($form);
        $this->setUrl($url);
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

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setTooltip(?string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getClickStatement(): ?string
    {
        if ($this->url === null) {
            return null;
        }

        $parts = ['click', $this->identifier, '"' . \str_replace('"', '#quot;', $this->url) . '"'];

        if ($this->tooltip !== null) {
            $parts[] = Helper::escape($this->tooltip);
        }

        if ($this->target !== null) {
            $parts[] = $this->target;
        }

        return \implode(' ', $parts);
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
