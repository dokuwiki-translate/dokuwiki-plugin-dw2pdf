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

    public function testBookmarkLevels() {
        $resolver = new HeadingResolver(new Config());

        $levels = [
            1,2,2,2,3,4,5,6,5,4,3,2,1, // index:0-12
            3,4,3,1,                   // 13-16
            2,3,4,2,3,4,1,             // 17-23
            3,4,3,2,1,                 // 24-28
            3,4,2,1,                   // 29-32
            3,5,6,5,6,4,6,3,1,         // 33-41
            3,6,4,5,6,4,3,6,2,1,       // 42-51
            2,3,2,3,3                  // 52-56
        ];
        $expectedbookmarklevels = [
            0,1,1,1,2,3,4,5,4,3,2,1,0,
            1,2,1,0,
            1,2,3,1,2,3,0,
            1,2,1,1,0,
            1,2,1,0,
            1,2,3,2,3,2,3,2,0,
            1,2,2,3,4,2,2,3,1,0,
            1,2,1,2,2
        ];
        foreach ($levels as $i => $level) {
            $actualbookmarklevel = $this->callInaccessibleMethod($resolver, 'calculateBookmarklevel', [$level]);
            $this->assertEquals($expectedbookmarklevels[$i], $actualbookmarklevel, "index:$i, lvl:$level");
        }
    }

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
