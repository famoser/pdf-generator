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

use PdfGenerator\Document\Pdf\PdfDocumentInterface;
use PdfGenerator\Document\Pdf\PdfFactoryInterface;
use PdfGenerator\Document\Pdf\PdfPageLayoutInterface;

class PdfFactory implements PdfFactoryInterface
{
    /**
     * @var string[][]
     */
    private $configuration = ['tcpdf' => []];

    /**
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * sets globals needed by TCPDF.
     */
    private function applyConfiguration()
    {
        $tcpdfConfig = $this->configuration['tcpdf'];
        if (isset($tcpdfConfig['font_path'])) {
            if (!\defined('K_PATH_FONTS')) {
                // TCPDF looks at the path this global variable defines
                \define('K_PATH_FONTS', $tcpdfConfig['font_path'] . \DIRECTORY_SEPARATOR);
            }
        }

        if (isset($tcpdfConfig['default_font_family'])) {
            if (!\defined('PDF_FONT_NAME_MAIN')) {
                // TCPDF chooses this font as the default
                \define('PDF_FONT_NAME_MAIN', $tcpdfConfig['default_font_family']);
            }
        }

        if (!\defined('K_TCPDF_THROW_EXCEPTION_ERROR')) {
            // TCPDF chooses this font as the default
            \define('K_TCPDF_THROW_EXCEPTION_ERROR', true);
        }

        if (!\defined('K_TCPDF_EXTERNAL_CONFIG')) {
            \define('K_TCPDF_EXTERNAL_CONFIG', true);
        }
    }

    /**
     * @param PdfPageLayoutInterface $pageLayout
     *
     * @throws \Exception
     *
     * @return PdfDocumentInterface
     */
    public function create(PdfPageLayoutInterface $pageLayout)
    {
        $this->applyConfiguration();

        return new PdfDocument($pageLayout);
    }
}
