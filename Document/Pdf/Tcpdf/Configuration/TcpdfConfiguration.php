<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf\Tcpdf\Configuration;

use App\Service\Report\Document\Pdf\Configuration\PrintConfiguration;
use App\Service\Report\Document\Pdf\Tcpdf\Pdf;

class TcpdfConfiguration extends PrintConfiguration
{
    const FONT_FAMILY_OPEN_SANS = 'opensans';

    /**
     * TcpdfConfiguration constructor.
     */
    public function __construct()
    {
        parent::__construct(new ConfigurationValidator());
    }

    /**
     * @param PrintConfiguration $configuration
     *
     * @throws \Exception
     *
     * @return TcpdfConfiguration
     */
    public static function createFromExisting(PrintConfiguration $configuration)
    {
        $new = new self();
        $new->setConfiguration($configuration->getConfiguration());

        return $new;
    }

    /**
     * @return bool
     */
    public function showBorder()
    {
        return $this->getBorderColor() !== null;
    }

    /**
     * @return bool
     */
    public function isFillEnabled()
    {
        return $this->getFillColor() !== null;
    }

    /**
     * @return string
     */
    public function getAlignment()
    {
        return $this->getTextAlign() === self::TEXT_ALIGN_RIGHT ? 'R' : 'L';
    }

    /**
     * @param Pdf $pdf
     */
    public function apply(Pdf $pdf)
    {
        $pdf->SetFont($this->getFontFamily(), $this->getFontWeight() === self::FONT_WEIGHT_BOLD ? 'b' : '', $this->getFontSize());
        $pdf->SetTextColor(...$this->getTextHex());

        if ($this->showBorder()) {
            $pdf->SetDrawColor(...$this->getBorderHex());
        }

        if ($this->isFillEnabled()) {
            $pdf->SetFillColor(...$this->getFillHex());
        }
    }

    /**
     * @return int[]
     */
    protected function getFillHex(): array
    {
        return self::hexStringToArray($this->getFillColor());
    }

    /**
     * @return int[]
     */
    protected function getTextHex(): array
    {
        return self::hexStringToArray($this->getTextColor());
    }

    /**
     * @return int[]
     */
    protected function getBorderHex(): array
    {
        return self::hexStringToArray($this->getBorderColor());
    }

    /**
     * @param string $hexString
     *
     * @return array
     */
    private static function hexStringToArray(string $hexString): array
    {
        return [hexdec(mb_substr($hexString, 1, 2)), hexdec(mb_substr($hexString, 3, 2)), hexdec(mb_substr($hexString, 5, 2))];
    }
}
