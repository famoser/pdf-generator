<?php

include __DIR__ . "/boot.php";

$pdf = new Cezpdf();
$pdf->DEBUGLEVEL = E_ALL;
$pdf->DEBUG = "variable";

$pdf->fontPath = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "examples" . DIRECTORY_SEPARATOR . "Roboto";
$pdf->selectFont("Roboto-Black.ttf");
$pdf->ezText("PDF \n with some colours ä", 30);

$result = $pdf->ezOutput(true);

saveContent($result);

var_dump($pdf->messages);
