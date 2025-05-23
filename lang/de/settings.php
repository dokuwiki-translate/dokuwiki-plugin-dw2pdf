<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @author andreash <aeheil@gmail.com>
 * @author Hella <hella.breitkopf@gmail.com>
 * @author Markus <mosesmami0@gmail.com>
 * @author Matthias Schulte <dokuwiki@lupo49.de>
 * @author Juergen-aus-Koeln <h-j-schuemmer@web.de>
 * @author F. Mueller-Donath <j.felix@mueller-donath.de>
 */
$lang['pagesize']              = 'Ein von mPDF unterstütztes Seitenformat. Normalerweise <code>A4</code> oder <code>letter</code>.';
$lang['orientation']           = 'Die Seiten-Ausrichtung';
$lang['orientation_o_portrait'] = 'Hochformat';
$lang['orientation_o_landscape'] = 'Querformat';
$lang['font-size']             = 'Die Schriftgröße für normalen Text in Punkt.';
$lang['doublesided']           = 'Doppelseitige Dokumente beginnen mit einer ungeraden Seite und werden fortgeführt mit Paaren von geraden und ungeraden Seiten. Einseitige Dokumente haben nur ungerade Seiten.';
$lang['toc']                   = 'Hinzufügen eines automatisch generierten Inhaltsverzeichnisses am Anfang der PDF-Datei (Anmerkung: kann dazu führen, dass eine leere Seite eingefügt wird, damit der Text bei einer ungeraden Seitenzahl beginnt; das Inhaltsverzeichnis selbst wird bei der Seitennummerierung nicht mitgezählt)';
$lang['toclevels']             = 'Oberste Ebene und maximale Tiefe des Inhaltsverzeichnisses. Standardmäßig werden die Werte aus der Wiki-Konfiguration <a href="#config___toptoclevel">toptoclevel</a> und <a href="#config___maxtoclevel">maxtoclevel</a> benutzt. Format: <code><i>&lt;top&gt;</i>-<i>&lt;max&gt;</i></code>';
$lang['headernumber']          = 'Nummerierung bei Überschriften einschalten';
$lang['maxbookmarks']          = 'Bis zu welcher Tiefe werden Lesezeichen angezeigt? (0=keine, 5=alle)';
$lang['template']              = 'Welches Template soll zur Formatierung der PDFs verwendet werden?';
$lang['output']                = 'Wie soll das PDF angezeigt werden?';
$lang['output_o_browser']      = 'Im Browser anzeigen';
$lang['output_o_file']         = 'PDF herunterladen';
$lang['usecache']              = 'Sollen PDFs zwischengespeichert werden? Eingebettete Grafiken werden dann nicht hinsichtlich ihrer Zugriffsberechtigungen geprüft (sicherheitskritische Option). ';
$lang['usestyles']             = 'Hier können komma-separiert Plugins angegeben werden, von denen die <code>style.css</code> oder <code>screen.css</code> für die PDF-Generierung verwendet werden sollen. Als Standard wird nur die <code>print.css</code> und <code>pdf.css</code> verwendet.';
$lang['qrcodescale']           = 'Größenanpassung des eingebetteten QR-Codes. Leer lassen oder <code>0</code> eingeben, um sie zu deaktivieren.';
$lang['showexportbutton']      = 'Zeige PDF Export Button (nur wenn vom Template unterstützt)';
