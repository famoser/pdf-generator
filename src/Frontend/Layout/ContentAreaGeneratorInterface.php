<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

interface ContentAreaGeneratorInterface
{
    /**
     * @param float[] $pageDimensions
     *
     * @return ContentArea[]
     */
    public function getContentAreas(array $pageDimensions, int $pageIndex): array;
}