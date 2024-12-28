<?php

include __DIR__ . "/boot.php";
include __DIR__ . "/Pdf.php";

$fontFile = __DIR__ . "/../fonts/Roboto/Roboto-Black.ttf";

$ttf = new TTFhelper($fontFile);
var_dump($ttf->getName());
