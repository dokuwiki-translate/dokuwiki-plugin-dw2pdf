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

    /** @var DepthNormalizer The depths of all headings, driving the numbering */
    protected DepthNormalizer $numberDepths;

    /** @var DepthNormalizer The depths of the bookmarked headings only */
    protected DepthNormalizer $bookmarkDepths;

    /** @var int[] The current counter for each depth */
    protected array $headerCount = [];

    /** @var int The depth of the heading numbered before this one */
    protected int $previousDepth = 0;

    /**
     * @param Config $config The configuration of the current export
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->numberDepths = new DepthNormalizer();
        $this->bookmarkDepths = new DepthNormalizer();
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
                $this->bookmarkDepths->normalize($level) - 1 // bookmark levels are zero indexed
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

        $depth = $this->numberDepths->normalize($level);

        // a heading closes all deeper levels
        if ($this->previousDepth > $depth) {
            for ($i = $depth + 1; $i <= $this->previousDepth; $i++) {
                $this->headerCount[$i] = 0;
            }
        }
        $this->headerCount[$depth] = ($this->headerCount[$depth] ?? 0) + 1;

        $prefix = '';
        for ($i = 1; $i <= $depth; $i++) {
            $prefix .= $this->headerCount[$i] . '.';
        }

        $this->previousDepth = $depth;
        return $prefix . ' ';
    }
}
