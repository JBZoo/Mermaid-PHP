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

namespace JBZoo\PHPUnit;

/**
 * Class CliColors
 * @package JBZoo\PHPUnit
 */
class CliColors
{
    protected static $colors = [
        'default' => 39,

        'black'   => 30, // white in PhpStorm
        'red'     => 31,
        'green'   => 32,
        'yellow'  => 33,
        'blue'    => 34,
        'magenta' => 35,
        'cyan'    => 36,
        'gray'    => 37,

        'dark_gray'     => 90,
        'light_red'     => 91,
        'light_green'   => 92,
        'light_yellow'  => 93,
        'light_blue'    => 94,
        'light_magenta' => 95,
        'light_cyan'    => 96,
        'white'         => 97, // black in PhpStorm
    ];

    /**
     * @return array
     */
    public static function buildDumperStyles()
    {
        return [
            'default' => self::$colors['gray'],
            'ref'     => self::$colors['dark_gray'],    // system info

            'meta' => self::$colors['magenta'],         // name of const
            'note' => self::$colors['blue'],            // name of classes

            'num'   => self::$colors['red'],
            'str'   => self::$colors['green'],
            'const' => self::$colors['yellow'],

            'key'   => self::$colors['cyan'],           // keys in array
            'index' => self::$colors['light_blue'],     // indexes in array

            'public'    => self::$colors['black'],
            'protected' => self::$colors['gray'],
            'private'   => self::$colors['gray'],
        ];
    }
}
