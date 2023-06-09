<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog\Font\Structure;

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\StreamObject;

class CMap extends BaseStructure
{
    /**
     * the name of that CMap
     * must equal to the name specified in the stream from useCMan.
     */
    private string $cMapName;

    /**
     * the system info which must match with the one specified on the CIDFont.
     */
    private CIDSystemInfo $cIDSystemInfo;

    /**
     * the actual encoding data.
     */
    private string $cMapData;

    public function getCMapName(): string
    {
        return $this->cMapName;
    }

    public function setCMapName(string $cMapName): void
    {
        $this->cMapName = $cMapName;
    }

    public function getCIDSystemInfo(): CIDSystemInfo
    {
        return $this->cIDSystemInfo;
    }

    public function setCIDSystemInfo(CIDSystemInfo $cIDSystemInfo): void
    {
        $this->cIDSystemInfo = $cIDSystemInfo;
    }

    public function getCMapData(): string
    {
        return $this->cMapData;
    }

    public function setCMapData(string $cMapData): void
    {
        $this->cMapData = $cMapData;
    }

    public function accept(CatalogVisitor $visitor): StreamObject
    {
        return $visitor->visitCMap($this);
    }
}
