<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf;

interface PdfPageLayoutInterface
{
    /**
     * @param PdfDocumentInterface $pdf
     */
    public function initializeLayout(PdfDocumentInterface $pdf);

    /**
     * @param PdfDocumentInterface $pdf
     */
    public function printHeader(PdfDocumentInterface $pdf);

    /**
     * the actual passed values for $currentPage and $totalPages may not be integers but placeholders.
     *
     * @param PdfDocumentInterface $pdf
     * @param int $currentPage
     * @param int $totalPages
     */
    public function printFooter(PdfDocumentInterface $pdf, $currentPage, $totalPages);
}
