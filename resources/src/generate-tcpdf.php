<?php

include __DIR__ . "/boot.php";
include __DIR__ . "/Pdf.php";

$pdf = new Pdf();

$pdf->AddPage();
$pdf->SetXY(20, 10);

$pdf->Text(10, 10, "a aa");

$result = $pdf->Output("pdf.pdf", 'S');

saveContent($result);
