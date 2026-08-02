<?php

namespace dokuwiki\plugin\dw2pdf\src;

/**
 * Maps the heading levels of a document onto consecutive depths
 *
 * Wiki authors skip heading levels freely, a level 1 heading may be followed by a level 4
 * one. Both the heading numbering and the PDF bookmark outline need a depth that is at most
 * one deeper than the depth of the heading before it. Levels are fed in document order and
 * are shifted up by as much as the skip that opened the current gap was worth, until a
 * heading closes that gap again.
 *
 * The first heading defines the baseline and keeps its level as its depth, so a document
 * that starts below the top level stays below it.
 *
 * Consumers that do not see every heading of the document need their own instance, because
 * the depths depend on the sequence of levels that was fed in.
 */
class DepthNormalizer
{
    /** @var int The level normalized before this one, -1 before the first one */
    protected int $lastLevel = -1;

    /** @var int The depth the current gap was opened at */
    protected int $gapStartDepth = 0;

    /** @var int How much the levels are currently shifted up */
    protected int $shift = 0;

    /**
     * Normalize the given heading level into a depth
     *
     * @param int $level from 1 (highest) to 6 (lowest)
     * @return int from 1 (highest), never more than one deeper than the previous result
     */
    public function normalize(int $level): int
    {
        if ($this->lastLevel === -1) {
            $this->lastLevel = $level;
        }

        $step = $level - $this->lastLevel;
        if ($step > 1) {
            $this->shift += $step - 1;
        }
        if ($step < 0) {
            // going back up releases as much of the shift as this level allows
            $this->shift = min($this->shift, $level - $this->gapStartDepth);
            $this->shift = max($this->shift, 0);
        }

        $depth = $level - $this->shift;

        if ($step > 1) {
            $this->gapStartDepth = $depth;
        }

        $this->lastLevel = $level;
        return $depth;
    }
}
