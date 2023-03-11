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

class Render
{
    public const THEME_DEFAULT = 'default';
    public const THEME_FOREST  = 'forest';
    public const THEME_DARK    = 'dark';
    public const THEME_NEUTRAL = 'neutral';

    public const DEFAULT_MERMAID_URL = 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';

    public static function html(Graph $graph, array $params = []): string
    {
        $theme     = (string)($params['theme'] ?? self::THEME_FOREST);
        $scriptUrl = (string)($params['mermaid_url'] ?? self::DEFAULT_MERMAID_URL);
        $showZoom  = (bool)($params['show-zoom'] ?? true);
        $isDebug   = (bool)($params['debug'] ?? false);

        $title     = (string)($params['title'] ?? '');
        $pageTitle = $title === '' ? $title : 'JBZoo - Mermaid Graph';

        /** @see https://mermaid-js.github.io/mermaid/#/mermaidAPI?id=loglevel */
        $mermaidParams = \json_encode([
            'startOnLoad' => true,
            'theme'       => $theme,
            'themeCSS'    => \implode(\PHP_EOL, [
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
        ], \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT);

        $debugCode = '';
        if ($isDebug) {
            $graphParams = \json_encode($graph->getParams(), \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT);

            $debugCode .= '<hr>';
            $debugCode .= '<pre><code>' . \htmlentities((string)$graph) . '</code></pre>';
            $debugCode .= '<hr>';
            $debugCode .= "<pre><code>Params = {$graphParams}</code></pre>";
        }

        $html = [
            '<!DOCTYPE html>',
            '<html lang="en">',
            '<head>',
            '    <meta charset="utf-8">',
            "    <title>{$pageTitle}</title>",
            '   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>',
            '<script type="module">',
            "        import mermaid from '{$scriptUrl}';",
            '</script>',
            '</head>',
            '<body>',

            $title !== '' ? "<h1>{$title}</h1><hr>" : '',

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
            '<script>
                $(document).on("click", "path", e => {
                    e.currentTarget.style.stroke = e.currentTarget.style.stroke ? "" : "red";
                });
            </script>',
            '</body>',
            '</html>',
        ];

        return \implode(\PHP_EOL, $html);
    }

    public static function escape(string $text): string
    {
        $text = \trim($text);
        $text = \htmlentities($text);

        $text = \str_replace(['&', '#lt;', '#gt;'], ['#', '<', '>'], $text);

        return "\"{$text}\"";
    }

    public static function getId(string $userFriendlyId): string
    {
        return \md5($userFriendlyId);
    }

    public static function getLiveEditorUrl(Graph $graph): string
    {
        $json = \json_encode([
            'code'    => (string)$graph,
            'mermaid' => ['theme' => 'forest'],
        ]);

        if ($json === false) {
            throw new \RuntimeException('Can\'t encode JSON');
        }

        $params = \base64_encode($json);

        return "https://mermaid-js.github.io/mermaid-live-editor/#/edit/{$params}";
    }
}
