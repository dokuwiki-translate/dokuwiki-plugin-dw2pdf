<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @author Petr Kajzar <petr.kajzar@centrum.cz>
 * @author Kamil Nešetřil <kamil.nesetril@volny.cz>
 * @author Jaroslav Lichtblau <jlichtblau@seznam.cz>
 */
$lang['pagesize']              = 'Formát stránky podporovaný mPDF. Obvykle <code>A4</code> nebo <code>letter</code>.';
$lang['orientation']           = 'Orientace stránky.';
$lang['orientation_o_portrait'] = 'Na výšku';
$lang['orientation_o_landscape'] = 'Na šířku';
$lang['font-size']             = 'Velikost písma pro normální text v bodech.';
$lang['doublesided']           = 'Dvoustránkový dokument začíná přidáním liché strany a obsahuje páry sudých a lichých stran. Jednostránkový dokument obsahuje pouze liché strany.';
$lang['toc']                   = 'Vložit automaticky vytvořený obsah do PDF (poznámka: může způsobit přidání prázdných stránek při začátku na liché straně, obsah je vždy na sudé straně a nemá žádné vlastní číslo strany)';
$lang['toclevels']             = 'Určit horní úroveň a maximální hloubku podúrovní přidaných do obsahu. Výchozí použité úrovně obsahu wiki jsou <a href="#config___toptoclevel">toptoclevel</a> a <a href="#config___maxtoclevel">maxtoclevel</a>. Formát: <code><i>&lt;top&gt;</i>-<i>&lt;max&gt;</i></code>';
$lang['maxbookmarks']          = 'Kolik úrovní nadpisů má být použito v záložkách PDF? <small>(0=žádné, 5=všechny)</small>';
$lang['template']              = 'Která šablona by měla být použita pro formátování PDF?';
$lang['output']                = 'Jak má být PDF zobrazeno uživateli?';
$lang['output_o_browser']      = 'Zobrazit v prohlížeči';
$lang['output_o_file']         = 'Stáhnout PDF';
$lang['usecache']              = 'Mají se PDF ukládat do mezipaměti? Vložené obrázky pak nebudou spravovány pomocí ACL, zakažte tuto možnost, pokud je to pro vás bezpečnostní problém.';
$lang['usestyles']             = 'Můžete zadat čárkami oddělený seznam zásuvných modulů, z nichž by měl být pro generování PDF použit <code>style.css</code> nebo <code>screen.css</code>. Ve výchozím nastavení jsou použity pouze <code>print.css</code> a <code>pdf.css</code>.';
$lang['qrcodesize']            = 'Velikost vloženého QR kódu (v pixelech <code><i>šířka</i><b>x</b><i>výška</i></code>). Vyprázdněním toto zakážete';
$lang['showexportbutton']      = 'Zobrazit tlačítko pro export PDF (pouze pokud je to podporováno vaší šablonou)';
