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

namespace JBZoo\MermaidPHP;

/**
 * Class Render
 * @package JBZoo\MermaidPHP
 */
class Render
{
    public const DEFAULT_VERSION = '8.6.4';

    public const THEME_DEFAULT = 'default';
    public const THEME_FOREST  = 'forest';
    public const THEME_DARK    = 'dark';
    public const THEME_NEUTRAL = 'neutral';

    /**
     * @param Graph         $graph
     * @param array<String> $params
     * @return string
     */
    public static function html(Graph $graph, array $params = []): string
    {
        //bool $showCode = false, string $version =
        $version = $params['version'] ?? self::DEFAULT_VERSION;
        $isDebug = $params['debug'] ?? false;
        $title = $params['title'] ?? '';
        $theme = $params['theme'] ?? self::THEME_FOREST;

        $pageTitle = $title ?: 'JBZoo - Mermaid Graph';
        $showZoom = $params['show-zoom'] ?? true;

        $scriptUrl = "https://unpkg.com/mermaid@{$version}/dist/mermaid.js";

        // @see https://mermaid-js.github.io/mermaid/#/mermaidAPI?id=loglevel
        $mermaidParams = \json_encode([
            'startOnLoad'         => true,
            'theme'               => $theme,
            'themeCSS'            => implode(PHP_EOL, [
                '.edgePath .path:hover {stroke-width:4px; cursor:pointer}',
                '.edgeLabel {border-radius:4px}',
                '.label {font-family:Source Sans Pro,Helvetica Neue,Arial,sans-serif;}',
            ]),
            'maxTextSize'         => 1000000, // almost no size limitation
            'loglevel'            => 'debug',
            'securityLevel'       => 'loose',
            'arrowMarkerAbsolute' => true,
            'flowchart'           => [
                'htmlLabels'     => true,
                'useMaxWidth'    => true,
                'diagramPadding' => 12,
                'curve'          => 'basis',
            ],
        ], JSON_PRETTY_PRINT);

        $debugCode = '';
        if ($isDebug) {
            $debugCode .= '<hr>';
            $debugCode .= '<pre><code>' . htmlentities((string)$graph) . '</code></pre>';
            $debugCode .= '<hr>';
            $graphParams = \json_encode($graph->getParams(), JSON_PRETTY_PRINT);
            $debugCode .= "<pre><code>Params = {$graphParams}</code></pre>";
        }

        return implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="en">',
            '<head>',
            '    <meta charset="utf-8">',
            "    <title>{$pageTitle}</title>",
            '   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>',
            "   <script src=\"{$scriptUrl}\"></script>",
            '</head>',
            '<body>',

            $title ? "<h1>{$title}</h1><hr>" : '',
            '    <div class="mermaid" style="margin-top:20px;">' . $graph . '</div>',

            $debugCode,

            $showZoom ?
                "<input type=\"button\" class=\"btn btn-primary\" id=\"zoom\" value=\"Zoom In\">
                <script>
                     mermaid.initialize({$mermaidParams});
                     $(function () {
                        $('#zoom').click(() => {
                            $('.mermaid').removeAttr('data-processed');
                            $('.mermaid').width($('.mermaid svg').css('max-width'));
                        });
                     });
                </script>"
                : '',
            '<script>$(document).on("click", "path", e => { e.currentTarget.style.stroke = e.currentTarget.style.stroke ? "" : "red"; })</script>',
            '</body>',
            '</html>',
        ]);
    }

    /**
     * @param string $text
     * @return string
     */
    public static function escape(string $text): string
    {
        $text = trim($text);
        $text = htmlentities($text);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $text = str_replace(['&', '#lt;', '#gt;'], ['#', '<', '>'], $text);

        return "\"{$text}\"";
    }

    /**
     * @param string $userFriendlyId
     * @return string
     */
    public static function getId(string $userFriendlyId): string
    {
        return md5($userFriendlyId);
    }

    /**
     * @param Graph $graph
     * @return string
     */
    public static function getLiveEditorUrl(Graph $graph): string
    {
        $params = base64_encode((string)json_encode([
            'code'    => (string)$graph,
            'mermaid' => [
                'theme' => 'forest'
            ]
        ]));

        return "https://mermaid-js.github.io/mermaid-live-editor/#/edit/{$params}";
    }
}
