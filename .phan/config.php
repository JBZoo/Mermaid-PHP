<?php

/**
 * JBZoo Toolbox - MermaidPHP
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    MermaidPHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/MermaidPHP
 */

$default = include __DIR__ . '/../vendor/jbzoo/codestyle/src/phan/default.php';

return array_merge($default, [
    'directory_list' => ['src']
]);
