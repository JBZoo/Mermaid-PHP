<?php
/**
 * JBZoo MermaidPHP
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    MermaidPHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/MermaidPHP
 */

namespace JBZoo\MermaidPHP;

/**
 * Class Node
 * @package JBZoo\MermaidPHP
 */
class Node
{
    public const SQUARE            = '["%s"]';
    public const ROUND             = '("%s")';
    public const CIRCLE            = '(("%s"))';
    public const ASYMMETRIC_SHAPE  = '>"%s"]';
    public const RHOMBUS           = '{"%s"}';
    public const HEXAGON           = '{{"%s"}}';
    public const PARALLELOGRAM     = '[/"%s"/]';
    public const PARALLELOGRAM_ALT = '[\"%s"\]';
    public const TRAPEZOID         = '[/"%s"\]';
    public const TRAPEZOID_ALT     = '[\"%s"/]';

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
        $this->identifier = $identifier;
        $this->setTitle($title);
        $this->setForm($form);
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
        return $this->title;
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
            $escaped = str_replace('&', '#', htmlentities($this->title));
            return $this->identifier . sprintf($this->form, $escaped) . ';';
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
