<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * This allocates content on the PDF.
 *
 * Importantly, the printer guarantees progress (i.e. with each print call, less to-be-printed content remains).
 * For this guarantee, the printer is allowed to disrespect boundaries (e.g. print content wider than the maxWidth).
 *
 * @implements AbstractBlockVisitor<Allocation>
 */
class AllocationVisitor extends AbstractBlockVisitor
{
    public function __construct(private ?float $maxWidth, private ?float $maxHeight)
    {
    }
}
