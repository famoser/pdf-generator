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

interface PdfFactoryInterface
{
    /**
     * configure implementation specific parameters
     * depending on the implementation, this must be called before the first pdf is created or it won't have the desired effects.
     *
     * @param string[][] $configuration
     */
    public function configure(array $configuration);

    /**
     * @param PdfPageLayoutInterface $pageLayout
     *
     * @return PdfDocumentInterface
     */
    public function create(PdfPageLayoutInterface $pageLayout);
}
