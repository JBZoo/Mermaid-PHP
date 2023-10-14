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

use JBZoo\MermaidPHP\ERDiagram\ERDiagram;
use JBZoo\MermaidPHP\Timeline\Timeline;

class Helper
{
    public static function escape(string $text): string
    {
        $text = \trim($text);
        $text = \htmlentities($text, \ENT_COMPAT);
        $text = \str_replace(['&', '#lt;', '#gt;'], ['#', '<', '>'], $text);

        return "\"{$text}\"";
    }

    public static function getId(string $userFriendlyId): string
    {
        return \md5($userFriendlyId);
    }

    public static function getLiveEditorUrl(ERDiagram|Graph|Timeline $mermaid): string
    {
        $json = \json_encode([
            'code'    => (string)$mermaid,
            'mermaid' => [
                'theme' => 'forest',
            ],
        ]);

        if ($json === false) {
            throw new \RuntimeException('Can\'t encode graph to JSON');
        }

        $params = \base64_encode($json);

        return "https://mermaid-js.github.io/mermaid-live-editor/#/edit/{$params}";
    }
}
