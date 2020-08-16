<?php

include __DIR__ . "/boot.php";
include __DIR__ . "/Pdf.php";

$fontFile = __DIR__ . "/../fonts/Roboto/Roboto-Black.ttf";
$outdir = __DIR__ ."/../generated/tcpdf-fonts/";


var_dump(TCPDF_FONTS::addTTFfont($fontFile, '', '', 32, $outdir, 3, 1, true));
