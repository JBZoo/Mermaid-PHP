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

namespace JBZoo\MermaidPHP\ERDiagram\Relation;

use JBZoo\MermaidPHP\ERDiagram\Entity\Entity;
use JBZoo\MermaidPHP\Helper;

abstract class Relation
{
    public const ZERO_OR_ONE  = '?';
    public const ZERO_OR_MORE = '*';
    public const ONE_OR_MORE  = '+';

    protected static bool $safeMode = false;
    protected Entity      $firstEntity;
    protected Entity      $secondEntity;
    protected string      $identifier  = '';
    protected ?string     $action      = '';
    protected ?string     $cardinality = null;
    protected bool        $identifying = true;

    abstract public function getLink(): string;

    /**
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        Entity $firstEntity,
        Entity $secondEntity,
        ?string $action = null,
        ?string $cardinality = null,
        bool $identifying = true,
    ) {
        /** @codingStandardsIgnoreEnd */
        $identifier         = $firstEntity->__toString() . $secondEntity->__toString();
        $this->identifier   = static::isSafeMode() ? Helper::getId($identifier) : $identifier;
        $this->firstEntity  = $firstEntity;
        $this->secondEntity = $secondEntity;
        $this->action       = $action;
        $this->cardinality  = $cardinality;
        $this->identifying  = $identifying;
    }

    public function __toString(): string
    {
        $action = $this->getAction();
        if ($action === null || $action === '') {
            $action = '""';
        }
        $action = ' : ' . $action;

        $firstEntity = (string)$this->firstEntity;

        $secondEntity = (string)$this->secondEntity;

        return \sprintf('%s %s %s%s', $firstEntity, static::getLink(), $secondEntity, $action);
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getCardinality(): ?string
    {
        return $this->cardinality;
    }

    public function setCardinality(?string $cardinality): void
    {
        $this->cardinality = $cardinality;
    }

    public function isIdentifying(): bool
    {
        return $this->identifying;
    }

    public function setIdentifying(bool $identifying): void
    {
        $this->identifying = $identifying;
    }

    public function getIdentification(): string
    {
        if (!$this->isIdentifying()) {
            return '..';
        }

        return '--';
    }

    public static function isSafeMode(): bool
    {
        return self::$safeMode;
    }
}
