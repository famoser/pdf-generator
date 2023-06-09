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

class PageGenerator implements PageGeneratorInterface
{
    /**
     * @var float[]
     */
    private array $pageDimensions;

    /**
     * PageGenerator constructor.
     *
     * @param float[] $pageDimensions
     */
    public function __construct(array $pageDimensions = null)
    {
        $this->pageDimensions = $pageDimensions ?? [210, 297]; // A4 is default
    }

    public function getNextPageDimensions(int $pageIndex): array
    {
        return $this->pageDimensions;
    }
}
