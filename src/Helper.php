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
 * Class Helper
 * @package JBZoo\MermaidPHP
 */
class Helper
{
    public const DEFAULT_VERSION = '8.4.3';

    /**
     * @param Graph         $graph
     * @param array<String> $params
     * @return string
     */
    public static function renderHtml(Graph $graph, array $params = []): string
    {
        //bool $showCode = false, string $version =
        $version = $params['version'] ?? self::DEFAULT_VERSION;
        $isDebug = $params['debug'] ?? false;
        $title = $params['title'] ?? '';

        $scriptUrl = "https://unpkg.com/mermaid@{$version}/dist/mermaid.js";
        $bootstrap = 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css';

        // @see https://mermaid-js.github.io/mermaid/#/mermaidAPI?id=loglevel
        $mermaidParams = \json_encode([
            'startOnLoad'         => true,
            'theme'               => 'forest', // default, forest, dark, neutral
            'themeCSS'            => implode(PHP_EOL, [
                '.edgePath .path:hover{stroke-width: 2px}',
                '.edgeLabel {border-radius: 4px}',
                '.label { font-family: Source Sans Pro,Helvetica Neue,Arial,sans-serif; }',
            ]),
            'loglevel'            => 'debug',
            'securityLevel'       => 'loose',
            'arrowMarkerAbsolute' => true,
            'flowchart'           => [
                'htmlLabels'  => true,
                'useMaxWidth' => true,
                'curve'       => 'basis',
            ],
        ], JSON_PRETTY_PRINT);

        $code = '';
        if ($isDebug) {
            $code .= '<hr>';
            $code .= '<pre><code>' . htmlentities((string)$graph) . '</code></pre>';
            $code .= '<hr>';
            $graphParams = \json_encode($graph->getParams(), JSON_PRETTY_PRINT);
            $code .= "<pre><code>Params = {$graphParams}</code></pre>";
        }

        return implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="en">',
            '<head>',
            '    <meta charset="utf-8">',
            "    <link rel=\"stylesheet\" href=\"{$bootstrap}\">",
            '</head>',
            '<body>',
            '    <main role="main" class="container">',
            $title ? "<h1>{$title}</h1><hr>" : '',
            '    <div class="mermaid" style="margin-top:20px;">',
            (string)$graph,
            '</div>',
            $code,
            '    </div>',
            '</main>',
            "    <script src=\"{$scriptUrl}\"></script>",
            "    <script>mermaid.initialize({$mermaidParams});</script>",
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
}
