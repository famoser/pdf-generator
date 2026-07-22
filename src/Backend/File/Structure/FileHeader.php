<?php

namespace Famoser\PdfGenerator\Backend\File\Structure;

use Famoser\PdfGenerator\Backend\File\Structure\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\File\StructureVisitor;

class FileHeader extends BaseStructure
{
    private float $version = 1.7;

    public function accept(StructureVisitor $visitor): string
    {
        return $visitor->visitFileHeader($this);
    }

    public function getVersion(): float
    {
        return $this->version;
    }
}
