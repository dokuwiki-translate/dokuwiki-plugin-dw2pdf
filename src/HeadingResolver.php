<?php

namespace dokuwiki\plugin\dw2pdf\src;

/**
 * Resolves heading markers into numbering prefixes and mPDF bookmarks
 *
 * Numbering and bookmark nesting depend on a heading's position within the assembled
 * document. That position is unknown while a single wiki page is rendered, and the
 * rendered page HTML is cached without any knowledge of the export it will become part
 * of. The renderer therefore emits a neutral marker for each heading and an instance of
 * this class, owned by the writer, substitutes those markers while the pages are
 * assembled.
 */
class HeadingResolver
{
    /** @var string Pattern matching a single heading marker */
    protected const MARKER_PATTERN = '/<!--dw2pdf-heading:(\d):([A-Za-z0-9+\/=]*)-->/';

    /** @var Config The configuration */
    protected Config $config;

    /** @var int[] The current counter for each heading level */
    protected array $headerCount = [];

    /** @var int The level of the heading resolved before this one */
    protected int $previousLevel = 0;

    /** @var int The level of the last heading passed to calculateBookmarklevel() */
    protected int $lastHeaderLevel = -1;

    /** @var int The bookmark level a skipped-level sequence started from */
    protected int $originalHeaderLevel = 0;

    /** @var int How much the bookmark levels are currently shifted up */
    protected int $difference = 0;

    /**
     * @param Config $config The configuration of the current export
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Create the marker a renderer emits in place of numbering and bookmark
     *
     * @param string $text The raw heading text
     * @param int $level from 1 (highest) to 6 (lowest)
     * @return string
     */
    public static function marker(string $text, int $level): string
    {
        return sprintf('<!--dw2pdf-heading:%d:%s-->', $level, base64_encode($text));
    }

    /**
     * Replace all heading markers in the given page
     *
     * Has to be called for the pages of an export in document order.
     *
     * @param string $html The rendered HTML of one wiki page
     * @return string
     */
    public function resolvePage(string $html): string
    {
        return preg_replace_callback(
            self::MARKER_PATTERN,
            fn($match) => $this->heading(base64_decode($match[2]), (int)$match[1]),
            $html
        );
    }

    /**
     * Build the numbering prefix and bookmark for a single heading
     *
     * @param string $text The raw heading text
     * @param int $level from 1 (highest) to 6 (lowest)
     * @return string
     */
    protected function heading(string $text, int $level): string
    {
        $headerPrefix = $this->getNumberPrefix($level);

        $bookmark = '';
        $maxbookmarklevel = $this->config->getMaxBookmarks();
        // 0: off, 1-6: show down to this level
        if ($maxbookmarklevel && $maxbookmarklevel >= $level) {
            $bookmark = sprintf(
                '<bookmark content="%s %s" level="%d" />',
                $headerPrefix,
                hsc($text),
                $this->calculateBookmarklevel($level)
            );
        }

        return $bookmark . $headerPrefix;
    }

    /**
     * Advance the counters and return the numbering prefix for the given heading
     *
     * Empty when numbered headings are disabled.
     *
     * @param int $level from 1 (highest) to 6 (lowest)
     * @return string
     */
    protected function getNumberPrefix(int $level): string
    {
        if (!$this->config->useNumberedHeaders()) return '';

        // a heading closes all deeper levels
        if ($this->previousLevel > $level) {
            for ($i = $level + 1; $i <= $this->previousLevel; $i++) {
                $this->headerCount[$i] = 0;
            }
        }
        $this->headerCount[$level] = ($this->headerCount[$level] ?? 0) + 1;

        $prefix = '';
        for ($i = 1; $i <= $level; $i++) {
            $prefix .= ($this->headerCount[$i] ?? 0) . '.';
        }

        $this->previousLevel = $level;
        return $prefix . ' ';
    }

    /**
     * Bookmark levels might increase maximal +1 per level.
     * (note: levels start at 1, bookmarklevels at 0)
     *
     * @param int $level 1 (highest) to 6 (lowest)
     * @return int
     */
    protected function calculateBookmarklevel($level)
    {
        if ($this->lastHeaderLevel == -1) {
            $this->lastHeaderLevel = $level;
        }
        $step = $level - $this->lastHeaderLevel;
        if ($step > 1) {
            $this->difference += $step - 1;
        }
        if ($step < 0) {
            $this->difference = min($this->difference, $level - $this->originalHeaderLevel);
            $this->difference = max($this->difference, 0);
        }

        $bookmarklevel = $level - $this->difference;

        if ($step > 1) {
            $this->originalHeaderLevel = $bookmarklevel;
        }

        $this->lastHeaderLevel = $level;
        return $bookmarklevel - 1; //zero indexed
    }
}
