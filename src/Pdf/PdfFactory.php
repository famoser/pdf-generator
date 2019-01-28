<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf;

class PdfFactory implements PdfFactoryInterface
{
    /**
     * @var string[][]
     */
    private $configuration = [[]];

    /**
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->configuration = array_merge($this->configuration, $configuration);
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
        return new PdfDocument($pageLayout);
    }
}
