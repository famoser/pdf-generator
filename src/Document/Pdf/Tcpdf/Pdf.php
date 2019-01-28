<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Pdf\Tcpdf;

use TCPDF;

/**
 * Overrides functionality of @TCPDF which can't be changed otherwise, and relays them to the @PdfDocument.
 *
 * Class Pdf
 */
class Pdf extends TCPDF
{
    /**
     * @var PdfDocument
     */
    private $wrapper;

    /**
     * CleanPdf constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param PdfDocument $wrapper
     */
    public function setWrapper(PdfDocument $wrapper): void
    {
        $this->wrapper = $wrapper;
    }

    /**
     * logo right & text left.
     */
    public function Header()
    {
        $this->wrapper->printHeader();
    }

    /**
     * bottom left author.
     */
    public function Footer()
    {
        $this->wrapper->printFooter();
    }

    /**
     * adds a new page if needed.
     *
     * @param int $h
     * @param string $y
     * @param bool $addPage
     *
     * @return bool|void
     */
    public function checkPageBreak($h = 0, $y = '', $addPage = true)
    {
        parent::checkPageBreak($h, $y, $addPage);
    }

    /**
     * the height to reach after which a new page will begin automatically.
     *
     * @return float
     */
    public function getMaxContentHeight()
    {
        return $this->PageBreakTrigger;
    }

    /**
     * the height to reach after which a new page will begin automatically.
     *
     * @return float
     */
    public function getMarginTop()
    {
        return $this->tMargin;
    }

    /**
     * @param $msg
     *
     * @throws \Exception
     */
    public function Error($msg)
    {
        throw new \Exception($msg);
    }
}
