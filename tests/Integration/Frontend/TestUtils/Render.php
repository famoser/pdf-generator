<?php

namespace Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils;

use Famoser\PdfGenerator\Frontend\Document;

trait Render
{
    protected function render(Document $document): string
    {
        $result = $document->save();
        file_put_contents('pdf.pdf', $result);

        return $result;
    }
}
