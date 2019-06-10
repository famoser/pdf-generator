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
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\StructureVisitor;

class CMap extends BaseStructure
{
    /**
     * the name of that CMap
     * must equal to the name specified in the stream from useCMan.
     *
     * @var string
     */
    private $cMapName;

    /**
     * the system info which must match with the one specified on the CIDFont.
     *
     * @var CIDSystemInfo
     */
    private $cIDSystemInfo;

    /**
     * the actual encoding data.
     *
     * @var string
     */
    private $cMapData;

    /**
     * @return string
     */
    public function getCMapName(): string
    {
        return $this->cMapName;
    }

    /**
     * @param string $cMapName
     */
    public function setCMapName(string $cMapName): void
    {
        $this->cMapName = $cMapName;
    }

    /**
     * @return CIDSystemInfo
     */
    public function getCIDSystemInfo(): CIDSystemInfo
    {
        return $this->cIDSystemInfo;
    }

    /**
     * @param CIDSystemInfo $cIDSystemInfo
     */
    public function setCIDSystemInfo(CIDSystemInfo $cIDSystemInfo): void
    {
        $this->cIDSystemInfo = $cIDSystemInfo;
    }

    /**
     * @return string
     */
    public function getCMapData(): string
    {
        return $this->cMapData;
    }

    /**
     * @param string $cMapData
     */
    public function setCMapData(string $cMapData): void
    {
        $this->cMapData = $cMapData;
    }

    /**
     * @param StructureVisitor $visitor
     *
     * @return BaseObject|BaseObject[]
     */
    public function accept(StructureVisitor $visitor)
    {
        return $visitor->visitCMap($this, $file);
    }
}
