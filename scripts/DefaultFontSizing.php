<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include '../vendor/autoload.php';

use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Font\DefaultFontType1Mapping;

$fontMapPath = '/usr/share/ghostscript/9.54.0/Resource/Init/Fontmap.GS';
$fontDir = '/usr/share/fonts/gsfonts';
$defaultFontSizeFilepath = '../src/IR/Structure/Document/Font/default_font_size.json';
$fontEnding = '.otf';

$fontMap = file_get_contents($fontMapPath);
$fontToFontFilename = [];
foreach (explode("\n", $fontMap) as $line) {
    if (strpos($line, '/') === 0) {
        $mapping = preg_split('/\s+/', $line);
        $fontToFontFilename[$mapping[0]] = $mapping[1];
    }
}

$fontFilenames = [];
foreach (DefaultFontType1Mapping::$mapping as $fontName => $fontStyles) {
    foreach ($fontStyles as $fontStyle => $baseFontName) {
        $fontname = $fontToFontFilename['/' . $baseFontName];
        $fontFilenames[$fontName][$fontStyle] = substr($fontname, 1) . $fontEnding;
    }
}

$document = new Document();
$sizing = [];
foreach ($fontFilenames as $fontName => $fontStyles) {
    foreach ($fontStyles as $fontStyle => $fontFilename) {
        $fontPath = $fontDir . '/' . $fontFilename;
        $font = $document->getOrCreateEmbeddedFont($fontPath);

        $sizing[$fontName][$fontStyle] = [
            'unitsInEm' => $font->getUnitsPerEm(),
            'ascender' => $font->getAscender(),
            'descender' => $font->getDescender(),
            'lineGap' => $font->getLineGap(),
        ];
    }
}

$sizingJson = json_encode($sizing, \JSON_PRETTY_PRINT);
file_put_contents($defaultFontSizeFilepath, $sizingJson);
