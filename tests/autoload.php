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

use JBZoo\PHPUnit\CliColors;
use JBZoo\Utils\Cli;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

// main autoload
if ($autoload = realpath('./vendor/autoload.php')) {
    require_once $autoload;
} else {
    echo 'Please execute "composer update" !' . PHP_EOL;
    exit(1);
}

define('PATH_ROOT', dirname(__DIR__));

/**
 * @param mixed  $variable Any kind of variable or resource
 * @param bool   $isDie    Call die() after dump, but if "make logs-vardump" is not running
 * @param string $label    Name of var
 * @return mixed|null
 * @SuppressWarnings(PHPMD.Superglobals)
 */
function dump($variable, $isDie = true)
{
    VarDumper::setHandler(function ($variable) {
        // Prepare vars
        $maxStringWidth = 1024 * 16; // Show first 16kb only

        // Configuration var dumper
        $varCloner = new VarCloner();
        $varCloner->setMaxItems(500);
        $varCloner->setMaxString($maxStringWidth);

        CliDumper::$defaultColors = true; // Forced colored
        $cliDumper = new CliDumper(null, 'UTF-8', CliDumper::DUMP_COMMA_SEPARATOR);
        $cliDumper->setMaxStringWidth($maxStringWidth);
        $cliDumper->setIndentPad('    ');
        $cliDumper->setDisplayOptions(['fileLinkFormat' => false]);
        $cliDumper->setStyles(CliColors::buildDumperStyles());

        $varClone = $varCloner->cloneVar($variable);

        $cliDumper->dump($varClone);
    });
    VarDumper::dump($variable);

    if ($isDie) {
        Cli::out('Dump_auto_die');
        die(1);
    }

    return $variable;
}
