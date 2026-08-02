<?php

namespace dokuwiki\plugin\dw2pdf\test;

use dokuwiki\plugin\dw2pdf\src\DepthNormalizer;
use DokuWikiTest;

/**
 * Tests for the normalization of skipped heading levels
 *
 * @group plugin_dw2pdf
 * @group plugins
 */
class DepthNormalizerTest extends DokuWikiTest {

    public function testDepths() {
        $normalizer = new DepthNormalizer();

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
        $expecteddepths = [
            1,2,2,2,3,4,5,6,5,4,3,2,1,
            2,3,2,1,
            2,3,4,2,3,4,1,
            2,3,2,2,1,
            2,3,2,1,
            2,3,4,3,4,3,4,3,1,
            2,3,3,4,5,3,3,4,2,1,
            2,3,2,3,3
        ];
        foreach ($levels as $i => $level) {
            $this->assertEquals($expecteddepths[$i], $normalizer->normalize($level), "index:$i, lvl:$level");
        }
    }

    /**
     * A document that starts below the top level keeps that level as its baseline
     */
    public function testFirstHeadingDefinesTheBaseline() {
        $normalizer = new DepthNormalizer();

        $this->assertSame(2, $normalizer->normalize(2));
        $this->assertSame(3, $normalizer->normalize(3));
        $this->assertSame(1, $normalizer->normalize(1));
    }

    /**
     * The depth of a heading depends on the sequence of levels fed to its normalizer
     *
     * This is why a consumer that skips headings needs an instance of its own.
     */
    public function testDepthDependsOnTheSequenceFedIn() {
        $all = new DepthNormalizer();
        $skipping = new DepthNormalizer();

        // the same document of four headings, but one normalizer only sees the first and the last
        $this->assertSame(1, $all->normalize(1));
        $this->assertSame(1, $skipping->normalize(1));

        $this->assertSame(2, $all->normalize(4));
        $this->assertSame(3, $all->normalize(6));

        $this->assertSame(3, $all->normalize(3));
        $this->assertSame(2, $skipping->normalize(3));
    }
}
