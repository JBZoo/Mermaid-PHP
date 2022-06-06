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
 * Class Helper
 * @package JBZoo\MermaidPHP
 */
class Helper
{
    /**
     * @param string $text
     * @return string
     */
    public static function escape(string $text): string
    {
        $text = \trim($text);
        $text = \htmlentities($text, \ENT_COMPAT);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $text = \str_replace(['&', '#lt;', '#gt;'], ['#', '<', '>'], $text);

        return "\"{$text}\"";
    }

    /**
     * @param string $userFriendlyId
     * @return string
     */
    public static function getId(string $userFriendlyId): string
    {
        return \md5($userFriendlyId);
    }

    /**
     * @param Graph $graph
     * @return string
     */
    public static function getLiveEditorUrl(Graph $graph): string
    {
        $params = \base64_encode(\json_encode([
            'code'    => (string)$graph,
            'mermaid' => [
                'theme' => 'forest'
            ]
        ], \JSON_THROW_ON_ERROR) ?: '');

        return "https://mermaid-js.github.io/mermaid-live-editor/#/edit/{$params}";
    }
}
