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

use JBZoo\MermaidPHP\ClassDiagram\ClassDiagram;
use JBZoo\MermaidPHP\ERDiagram\ERDiagram;
use JBZoo\MermaidPHP\Timeline\Timeline;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class Render
{
    public const string THEME_DEFAULT = 'default';
    public const string THEME_FOREST  = 'forest';
    public const string THEME_DARK    = 'dark';
    public const string THEME_NEUTRAL = 'neutral';

    public const string DEFAULT_MERMAID_URL = 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';

    /**
     * Valid values for the "mermaid.kind" descriptor: they bind the loader flavor to its source atomically.
     *  - esm-url    : ES module imported from a URL (the historical default, code-split, needs the network).
     *  - umd-url    : self-contained UMD bundle served from the user's own domain via <script src>.
     *  - umd-inline : self-contained UMD bundle read from a local file and embedded straight into the page.
     */
    private const array MERMAID_KINDS = ['esm-url', 'umd-url', 'umd-inline'];

    /** Safety cap for an inlined bundle (the standalone UMD build is ~3.5 MB; 20 MB leaves generous head-room). */
    private const int MAX_INLINE_BYTES = 20_000_000;

    /**
     * @param array<string, mixed> $params
     */
    public static function html(ClassDiagram|ERDiagram|Graph|Timeline $graph, array $params = []): string
    {
        $theme    = (string)($params['theme'] ?? self::THEME_FOREST);
        $showZoom = (bool)($params['show-zoom'] ?? true);
        $isDebug  = (bool)($params['debug'] ?? false);

        $title     = (string)($params['title'] ?? '');
        $pageTitle = $title === '' ? $title : 'JBZoo - Mermaid Graph';

        [$mermaidKind, $mermaidSource] = self::resolveMermaidSource($params);

        /** @see https://mermaid-js.github.io/mermaid/#/mermaidAPI?id=loglevel */
        $mermaidParams = \json_encode([
            'startOnLoad' => false, // we render explicitly via mermaid.run() below
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
        ], \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_HEX_AMP);

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
            '    <title>' . \htmlspecialchars($pageTitle, \ENT_QUOTES) . '</title>',
            '</head>',
            '<body>',

            $title !== '' ? '<h1>' . \htmlspecialchars($title, \ENT_QUOTES) . '</h1><hr>' : '',

            '    <div class="mermaid" style="margin-top:20px;">' . $graph->__toString() . '</div>',

            $debugCode,

            self::buildInteractionScripts($showZoom),

            // The loader + bootstrap live AFTER the diagram markup so a synchronous UMD/inline script
            // cannot fire before the DOM (or the config) is ready.
            self::buildMermaidBlock($mermaidKind, $mermaidSource, $mermaidParams),

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

    public static function getLiveEditorUrl(ERDiagram|Graph|Timeline $graph): string
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

    /**
     * Resolve how Mermaid should be loaded into the page.
     *
     * @param  array<string, mixed>        $params
     * @return array{0: string, 1: string} [kind, source]
     */
    private static function resolveMermaidSource(array $params): array
    {
        $hasDescriptor = \array_key_exists('mermaid', $params);
        $hasLegacyUrl  = \array_key_exists('mermaid_url', $params);

        if ($hasDescriptor && $hasLegacyUrl) {
            throw new Exception('Use either the "mermaid" descriptor or "mermaid_url", not both.');
        }

        if ($hasDescriptor) {
            return self::resolveMermaidDescriptor($params['mermaid']);
        }

        $url = (string)($params['mermaid_url'] ?? self::DEFAULT_MERMAID_URL);
        if ($url === '' || \str_contains($url, "\0")) {
            throw new Exception('The "mermaid_url" option must be a non-empty string without NUL bytes.');
        }

        return ['esm-url', $url];
    }

    /**
     * @return array{0: string, 1: string} [kind, source]
     */
    private static function resolveMermaidDescriptor(mixed $descriptor): array
    {
        if (!\is_array($descriptor)) {
            throw new Exception('The "mermaid" option must be an array like ["kind" => ..., "source" => ...].');
        }

        $kind   = $descriptor['kind'] ?? null;
        $source = $descriptor['source'] ?? null;

        if (!\in_array($kind, self::MERMAID_KINDS, true)) {
            throw new Exception('The "mermaid.kind" must be one of: ' . \implode(', ', self::MERMAID_KINDS) . '.');
        }

        if (!\is_string($source) || $source === '' || \str_contains($source, "\0")) {
            throw new Exception('The "mermaid.source" must be a non-empty string without NUL bytes.');
        }

        if ($kind === 'umd-inline') {
            // Canonicalize to the validated real path so read-back cannot follow a different path than validation.
            $source = self::assertReadableLocalFile($source);
        }

        return [$kind, $source];
    }

    /**
     * A local file whose contents get embedded is a fresh server-side trust boundary, so validate it beyond
     * "is readable": reject NUL bytes and stream wrappers, require a real regular file. Returns the canonical path;
     * the size cap is enforced at read time (filesize() can lie for procfs-like files) — see readInlineScriptBase64().
     */
    private static function assertReadableLocalFile(string $path): string
    {
        if (\str_contains($path, "\0")) {
            throw new Exception('The mermaid inline source must not contain a NUL byte.');
        }

        if (\preg_match('~^[a-zA-Z][a-zA-Z0-9+.\-]*://~', $path) === 1) {
            throw new Exception("The mermaid inline source must be a local file, not a stream wrapper: {$path}");
        }

        $realPath = \realpath($path);
        if ($realPath === false || !\is_file($realPath)) {
            throw new Exception("The mermaid inline file was not found or is not a regular file: {$path}");
        }

        return $realPath;
    }

    /**
     * Build the loader + a single post-DOM bootstrap (initialize + explicit run) for the chosen source.
     */
    private static function buildMermaidBlock(string $kind, string $source, string $mermaidParams): string
    {
        // Initialize immediately, but defer the render to window.load so web fonts are ready (Mermaid's
        // historical startOnLoad behavior), falling back to an immediate run if load already happened.
        $bootstrap = [
            "    mermaid.initialize({$mermaidParams});",
            '    if (document.readyState === "complete") {',
            '        mermaid.run();',
            '    } else {',
            '        window.addEventListener("load", function () { mermaid.run(); }, {once: true});',
            '    }',
        ];

        if ($kind === 'esm-url') {
            $specifier = \json_encode(
                $source,
                \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES
                | \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_HEX_AMP,
            );

            return \implode(\PHP_EOL, [
                '<script type="module">',
                "    import mermaid from {$specifier};",
                '    window.mermaid = mermaid;',
                ...$bootstrap,
                '</script>',
            ]);
        }

        $loader = $kind === 'umd-url'
            ? '<script src="' . \htmlspecialchars($source, \ENT_QUOTES) . '"></script>'
            : self::buildInlineLoader($source);

        return \implode(\PHP_EOL, [
            $loader,
            '<script>',
            ...$bootstrap,
            '</script>',
        ]);
    }

    /**
     * Embed a local UMD bundle without putting arbitrary JavaScript into raw <script> text (which the HTML
     * tokenizer can terminate early on "</script", "<!--", "<script" sequences). The bytes travel as base64
     * (an HTML-safe alphabet) and are decoded + injected as a real script element, executed synchronously.
     */
    private static function buildInlineLoader(string $realPath): string
    {
        $base64 = self::readInlineScriptBase64($realPath);

        return \implode(\PHP_EOL, [
            '<script>',
            '    (function () {',
            "        var bytes = Uint8Array.from(atob(\"{$base64}\"), function (c) { return c.charCodeAt(0); });",
            '        var code = new TextDecoder("utf-8").decode(bytes);',
            '        var element = document.createElement("script");',
            '        element.textContent = code;',
            '        document.head.appendChild(element);',
            '    })();',
            '</script>',
        ]);
    }

    private static function readInlineScriptBase64(string $realPath): string
    {
        // Bound the read to the cap + 1 byte: memory stays bounded even if the file lies about its size.
        $contents = \file_get_contents($realPath, false, null, 0, self::MAX_INLINE_BYTES + 1);
        if ($contents === false) {
            throw new Exception("Unable to read the mermaid inline file: {$realPath}");
        }

        if (\strlen($contents) > self::MAX_INLINE_BYTES) {
            throw new Exception("The mermaid inline file is too large to embed: {$realPath}");
        }

        return \base64_encode($contents);
    }

    /**
     * Native (jQuery-free) interaction handlers: a "Zoom In" button and a delegated "click a path to
     * highlight it" listener. Delegation on `document` is required because Mermaid creates the SVG paths later.
     */
    private static function buildInteractionScripts(bool $showZoom): string
    {
        $blocks = [];

        if ($showZoom) {
            $blocks[] = \implode(\PHP_EOL, [
                '<input type="button" class="btn btn-primary" id="zoom" value="Zoom In">',
                '<script>',
                '    document.getElementById("zoom").addEventListener("click", function () {',
                '        document.querySelectorAll(".mermaid").forEach(function (element) {',
                '            element.removeAttribute("data-processed");',
                '            var svg = element.querySelector("svg");',
                '            if (svg !== null) {',
                '                element.style.width = window.getComputedStyle(svg).maxWidth;',
                '            }',
                '        });',
                '    });',
                '</script>',
            ]);
        }

        $blocks[] = \implode(\PHP_EOL, [
            '<script>',
            '    document.addEventListener("click", function (event) {',
            '        var target = event.target;',
            '        var path = (target && typeof target.closest === "function") ? target.closest("path") : null;',
            '        if (path !== null && path.closest(".mermaid") !== null) {',
            '            path.style.stroke = path.style.stroke ? "" : "red";',
            '        }',
            '    });',
            '</script>',
        ]);

        return \implode(\PHP_EOL, $blocks);
    }
}
