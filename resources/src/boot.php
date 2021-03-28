<?php

include __DIR__ . "/../vendor/autoload.php";


function saveContent($content) {
    $targetFolder = realpath(__DIR__ . "/../generated");
    $fileName = "pdf";
    file_put_contents($targetFolder . "/" . $fileName . ".pdf", $content);

    exec("qpdf --qdf --object-streams=disable " . $targetFolder . "/" . $fileName . ".pdf " . $targetFolder . "/" . $fileName . "_normalized.pdf");
}