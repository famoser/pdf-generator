<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integration\Frontend\LinearDocument;

use PdfGenerator\Frontend\LinearDocument;
use PHPUnit\Framework\TestCase;

abstract class AbstractLinearDocumentTest extends TestCase
{
    protected function render(LinearDocument $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
