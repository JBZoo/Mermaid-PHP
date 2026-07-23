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

namespace JBZoo\PHPUnit;

use JBZoo\MermaidPHP\Exception;
use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Node;
use JBZoo\MermaidPHP\Render;

final class RenderTest extends PHPUnit
{
    /** @var string[] */
    private array $tmpFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tmpFiles as $file) {
            if (\is_file($file)) {
                \unlink($file);
            }
        }
        $this->tmpFiles = [];

        parent::tearDown();
    }

    public function testDefaultUsesEsmCdnAndNoJquery(): void
    {
        $html = Render::html($this->graph());

        isContain('import mermaid from', $html);
        isContain(Render::DEFAULT_MERMAID_URL, $html);
        isContain('mermaid.run();', $html);

        // The whole point of the GDPR fix: jQuery must be gone from the output entirely.
        isNotContain('jquery', $html, true);
        isNotContain('code.jquery.com', $html, true);
        isNotContain('$(', $html);
    }

    public function testLegacyMermaidUrlStillWorks(): void
    {
        $url  = 'https://example.com/custom/mermaid.esm.min.mjs';
        $html = Render::html($this->graph(), ['mermaid_url' => $url]);

        isContain("import mermaid from \"{$url}\"", $html);
        isNotContain(Render::DEFAULT_MERMAID_URL, $html);
    }

    public function testUmdUrlEmitsClassicScriptSrc(): void
    {
        $html = Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-url', 'source' => '/assets/js/mermaid.min.js'],
        ]);

        isContain('<script src="/assets/js/mermaid.min.js"></script>', $html);
        isContain('mermaid.run();', $html);
        isNotContain('import mermaid from', $html);
    }

    public function testUmdInlineEmbedsFileContentsAsBase64(): void
    {
        $contents = 'window.mermaid={initialize:function(){},run:function(){}};/*INLINE_MARKER*/';
        $file     = $this->tmpFile($contents);

        $html = Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => $file],
        ]);

        // The bytes are embedded HTML-safely as base64 and decoded in the browser, never as raw script text.
        isContain(\base64_encode($contents), $html);
        isContain('atob(', $html);
        isContain('TextDecoder', $html);
        isContain('mermaid.run();', $html);
        isNotContain('import mermaid from', $html);
        isNotContain('/*INLINE_MARKER*/', $html);
    }

    public function testUmdInlineIsTokenizerSafe(): void
    {
        // A bundle carrying "</script" (and the double-escape triggers) must not appear as raw script text.
        $contents = 'var a = "<!--", b = "<script>", c = "</script>";';
        $file     = $this->tmpFile($contents);

        $html = Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => $file],
        ]);

        isContain(\base64_encode($contents), $html);
        isNotContain('c = "</script>";', $html);
    }

    public function testInlineNulByteIsRejected(): void
    {
        $this->expectException(Exception::class);

        Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => "/tmp/mermaid\0.js"],
        ]);
    }

    public function testDescriptorAndLegacyUrlAreMutuallyExclusive(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not both');

        Render::html($this->graph(), [
            'mermaid_url' => Render::DEFAULT_MERMAID_URL,
            'mermaid'     => ['kind' => 'umd-url', 'source' => '/x.js'],
        ]);
    }

    public function testInvalidKindThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('mermaid.kind');

        Render::html($this->graph(), ['mermaid' => ['kind' => 'nope', 'source' => '/x.js']]);
    }

    public function testEmptySourceThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('mermaid.source');

        Render::html($this->graph(), ['mermaid' => ['kind' => 'umd-url', 'source' => '']]);
    }

    public function testNonArrayDescriptorThrows(): void
    {
        $this->expectException(Exception::class);

        Render::html($this->graph(), ['mermaid' => 'https://example.com/mermaid.min.js']);
    }

    public function testInlineMissingFileThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not found or is not a regular file');

        Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => '/no/such/file-xyz.js'],
        ]);
    }

    public function testInlineDirectoryThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not a regular file');

        Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => \sys_get_temp_dir()],
        ]);
    }

    public function testInlineStreamWrapperIsRejected(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not a stream wrapper');

        Render::html($this->graph(), [
            'mermaid' => ['kind' => 'umd-inline', 'source' => 'php://temp'],
        ]);
    }

    public function testShowZoomFalseStillInitializesAndRuns(): void
    {
        $html = Render::html($this->graph(), ['show-zoom' => false]);

        isContain('mermaid.initialize(', $html);
        isContain('mermaid.run();', $html);
        isNotContain('id="zoom"', $html);
    }

    public function testShowZoomTrueEmitsButton(): void
    {
        $html = Render::html($this->graph(), ['show-zoom' => true]);

        isContain('id="zoom"', $html);
    }

    public function testTitleIsHtmlEscaped(): void
    {
        $html = Render::html($this->graph(), ['title' => '<img src=x onerror=alert(1)>&"']);

        isContain('&lt;img src=x onerror=alert(1)&gt;', $html);
        isNotContain('<h1><img src=x', $html);
    }

    public function testEsmUrlWithQuoteCannotBreakOutOfTheImport(): void
    {
        $html = Render::html($this->graph(), ['mermaid_url' => 'https://e.com/a.mjs";alert(1)//']);

        // The raw quote+payload must not survive verbatim inside the module script.
        isNotContain('a.mjs";alert(1)', $html);
    }

    public function testAllDiagramEntryPointsShareTheRenderer(): void
    {
        // Graph::renderHtml delegates to Render::html; a smoke check that the pipeline is intact.
        $html = $this->graph()->renderHtml(['mermaid' => ['kind' => 'umd-url', 'source' => '/m.js']]);

        isContain('<script src="/m.js"></script>', $html);
        isContain((string)$this->graph(), $html);
    }

    private function graph(): Graph
    {
        Node::safeMode(false);

        return (new Graph())
            ->addNode($nodeA = new Node('A', 'Node A'))
            ->addNode($nodeB = new Node('B', 'Node B'))
            ->addLink(new \JBZoo\MermaidPHP\Link($nodeA, $nodeB));
    }

    private function tmpFile(string $contents): string
    {
        $path = \tempnam(\sys_get_temp_dir(), 'mermaid_render_test_');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a temp file for the test');
        }

        \file_put_contents($path, $contents);
        $this->tmpFiles[] = $path;

        return $path;
    }
}
