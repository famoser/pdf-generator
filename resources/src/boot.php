<?php

include __DIR__ . "/../vendor/autoload.php";


function saveContent($content) {
    $targetFolder = __DIR__ . "/../generated";
    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0777, true);
    }

    $fileName = "pdf";
    file_put_contents($targetFolder . "/" . $fileName . ".pdf", $content);

    exec("qpdf --qdf --object-streams=disable " . $targetFolder . "/" . $fileName . ".pdf " . $targetFolder . "/" . $fileName . "_normalized.pdf");
}
