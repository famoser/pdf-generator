<?php

namespace Famoser\PdfGenerator\Backend\Catalog\Font\Structure;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\StreamObject;

readonly class CMap extends BaseStructure
{
    /**
     * @param string        $cMapName      the name of that CMap
     *                                     must equal to the name specified in the stream from useCMan
     * @param CIDSystemInfo $cIDSystemInfo the system info
     *                                     must match with the one specified on the CIDFont
     * @param string        $cMapData      the actual encoding data
     */
    public function __construct(private string $cMapName, private CIDSystemInfo $cIDSystemInfo, private string $cMapData)
    {
        parent::__construct();
    }

    public function getCMapName(): string
    {
        return $this->cMapName;
    }

    public function getCIDSystemInfo(): CIDSystemInfo
    {
        return $this->cIDSystemInfo;
    }

    public function getCMapData(): string
    {
        return $this->cMapData;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitCMap($this);
    }
}
