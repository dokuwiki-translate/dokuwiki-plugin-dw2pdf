<?php

namespace dokuwiki\plugin\dw2pdf\test;

use dokuwiki\plugin\dw2pdf\src\Config;
use dokuwiki\plugin\dw2pdf\src\HeadingResolver;
use DokuWikiTest;

/**
 * Tests for the resolution of the heading markers emitted by the renderer
 *
 * @group plugin_dw2pdf
 * @group plugins
 */
class HeadingResolverTest extends DokuWikiTest {

    /**
     * The heading text has to survive being marked and resolved again
     */
    public function testMarkerRoundtrip() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 1, 'maxbookmarks' => 5]));

        $html = '<h1>' . HeadingResolver::marker('Wüst & "quoted" <tag>', 1) . '<a name="x">…</a></h1>';
        $resolved = $resolver->resolvePage($html);

        $this->assertStringNotContainsString('dw2pdf-heading', $resolved);
        $this->assertStringContainsString(
            '<bookmark content="1.  Wüst &amp; &quot;quoted&quot; &lt;tag&gt;" level="0" />',
            $resolved
        );
        $this->assertStringContainsString('/>1. <a name="x">', $resolved);
    }

    /**
     * With numbering and bookmarks disabled only the marker itself is removed
     */
    public function testDisabledOptions() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 0, 'maxbookmarks' => 0]));

        $html = '<h1>' . HeadingResolver::marker('Header', 1) . '<a name="x">Header</a></h1>';

        $this->assertSame('<h1><a name="x">Header</a></h1>', $resolver->resolvePage($html));
    }

    /**
     * Numbering continues across pages
     */
    public function testNumberingAcrossPages() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 1, 'maxbookmarks' => 0]));

        $this->assertSame('1. 1.1. ', $resolver->resolvePage(
            HeadingResolver::marker('One', 1) . HeadingResolver::marker('Sub', 2)
        ));
        $this->assertSame('2. ', $resolver->resolvePage(HeadingResolver::marker('Two', 1)));
    }

    /**
     * Skipped heading levels do not produce gaps in the numbering
     */
    public function testNumberingOfSkippedLevels() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 1, 'maxbookmarks' => 0]));

        $this->assertSame('1. 1.1. 1.2. 1.2.1. ', $resolver->resolvePage(
            HeadingResolver::marker('Top', 1) .
            HeadingResolver::marker('Deep A', 4) .
            HeadingResolver::marker('Less Deep', 3) .
            HeadingResolver::marker('Deep B', 4)
        ));
    }

    /**
     * Numbering and bookmark nesting agree, even though bookmarks skip deeper headings
     */
    public function testNumberingAndBookmarksAgree() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 1, 'maxbookmarks' => 3]));

        $resolved = $resolver->resolvePage(
            HeadingResolver::marker('Top', 1) .
            HeadingResolver::marker('Deep', 4) .
            HeadingResolver::marker('Less Deep', 3)
        );

        $this->assertSame(
            '<bookmark content="1.  Top" level="0" />1. ' .
            '1.1. ' .
            '<bookmark content="1.2.  Less Deep" level="1" />1.2. ',
            $resolved
        );
    }

    /**
     * Bookmarks are only emitted down to the configured level
     */
    public function testMaxBookmarks() {
        $resolver = new HeadingResolver(new Config(['headernumber' => 0, 'maxbookmarks' => 2]));

        $resolved = $resolver->resolvePage(
            HeadingResolver::marker('One', 1) .
            HeadingResolver::marker('Two', 2) .
            HeadingResolver::marker('Three', 3)
        );

        $this->assertSame(
            '<bookmark content=" One" level="0" /><bookmark content=" Two" level="1" />',
            $resolved
        );
    }
}
