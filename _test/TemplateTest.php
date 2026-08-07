<?php

namespace dokuwiki\plugin\dw2pdf\test;

use dokuwiki\plugin\dw2pdf\src\Config;
use dokuwiki\plugin\dw2pdf\src\PageCollector;
use dokuwiki\plugin\dw2pdf\src\Template;
use DokuWikiTest;

/**
 * @group plugin_dw2pdf
 * @group plugins
 */
class TemplateTest extends DokuWikiTest
{
    /** @var string The page ID used as context in all tests */
    protected string $pageid = 'playground:templatepage';

    public function setUp(): void
    {
        parent::setUp();
        $_REQUEST = [];
    }

    /**
     * Create a template with a page context set up
     *
     * @return Template
     */
    protected function getTemplate(): Template
    {
        global $ID, $INPUT;
        $INPUT->set('book_title', 'Export Title');
        $ID = $this->pageid;
        saveWikiText($ID, 'template test', 'create');

        $config = new Config([
            'qrcodescale' => 1.5,
        ]);

        $collector = new PageCollector($config);
        $template = new Template($config);
        $template->setContext($collector, $ID, 'username');

        return $template;
    }

    /**
     * Template placeholders (title, username, QR codes, links) should be expanded from context data.
     */
    public function testPlaceholderReplacement(): void
    {
        global $conf;

        $template = $this->getTemplate();
        $html = self::callInaccessibleMethod($template, 'replacePlaceholders', [
            "Page Number: @PAGE@\n" .
            "Total Pages: @PAGES@\n" .
            "Document Title: @TITLE@\n" .
            "Wiki Title: @WIKI@\n" .
            "Wiki URL: @WIKIURL@\n" .
            "Date: @DATE@\n" .
            "User: @USERNAME@\n" .
            "Base Path: @BASE@\n" .
            "Include Dir: @INC@\n" .
            "Template Base Path: @TPLBASE@\n" .
            "Template Include Dir: @TPLINC@\n" .
            "Page ID: @ID@\n" .
            "Revision: @UPDATE@\n" .
            "Page URL: @PAGEURL@\n" .
            "QR Code: @QRCODE@\n"
        ]);

        $this->assertStringContainsString('Page Number: {PAGENO}', $html);
        $this->assertStringContainsString('Total Pages: {nbpg}', $html);
        $this->assertStringContainsString('Document Title: Export Title', $html);
        $this->assertStringContainsString('Wiki Title: ' . $conf['title'], $html);
        $this->assertStringContainsString('Wiki URL: ' . DOKU_URL, $html);
        $this->assertStringNotContainsString('@DATE@', $html);
        $this->assertStringContainsString('User: username', $html);
        $this->assertStringContainsString('Base Path: ' . DOKU_BASE, $html);
        $this->assertStringContainsString('Include Dir: ' . DOKU_INC, $html);
        $this->assertStringContainsString('Template Base Path: ' . DOKU_BASE . 'lib/plugins/dw2pdf/tpl/default/', $html);
        $this->assertStringContainsString('Template Include Dir: ' . DOKU_INC . 'lib/plugins/dw2pdf/tpl/default/', $html);
        $this->assertStringContainsString('Page ID: ' . $this->pageid, $html);

        $revisionDate = dformat(filemtime(wikiFN($this->pageid)));
        $this->assertStringContainsString('Revision: ' . $revisionDate, $html);

        $pageUrl = wl($this->pageid, [], true, '&');
        $this->assertStringContainsString('Page URL: ' . $pageUrl, $html);
        $this->assertStringContainsString('<barcode', $html);
        $this->assertStringContainsString('size="1.5"', $html);
        $this->assertStringNotContainsString('@QRCODE@', $html);
    }

    /**
     * The ordered variant of a template file should be preferred and have its placeholders replaced.
     */
    public function testOrderedFile(): void
    {
        $template = $this->getTemplate();

        $html = $template->getHTML('header', 'odd');

        $this->assertStringContainsString('class="pdfheader"', $html);
        $this->assertStringContainsString('{PAGENO}/{nbpg}', $html);
        $this->assertStringContainsString('Export Title', $html);
    }

    /**
     * A missing ordered variant should fall back to the generic template file.
     */
    public function testFallbackToGenericFile(): void
    {
        $template = $this->getTemplate();

        $this->assertSame($template->getHTML('citation'), $template->getHTML('citation', 'first'));
    }

    /**
     * A template file that does not exist should result in empty output.
     */
    public function testMissingFile(): void
    {
        $template = $this->getTemplate();

        $this->assertSame('', $template->getHTML('cover'));
    }
}
