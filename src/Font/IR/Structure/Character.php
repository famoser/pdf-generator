<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure;

use PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;

class Character
{
    /**
     * @var int
     */
    private $unicodePoint;

    /**
     * @var PostScriptInfo
     */
    private $postScriptInfo;

    /**
     * @var BoundingBox
     */
    private $boundingBox;

    /**
     * @var GlyfTable
     */
    private $glyfTable;

    /**
     * @var LongHorMetric
     */
    private $longHorMetric;

    /**
     * @return int
     */
    public function getUnicodePoint(): int
    {
        return $this->unicodePoint;
    }

    /**
     * @param int $unicodePoint
     */
    public function setUnicodePoint(int $unicodePoint): void
    {
        $this->unicodePoint = $unicodePoint;
    }

    /**
     * @return PostScriptInfo
     */
    public function getPostScriptInfo(): PostScriptInfo
    {
        return $this->postScriptInfo;
    }

    /**
     * @param PostScriptInfo $postScriptInfo
     */
    public function setPostScriptInfo(PostScriptInfo $postScriptInfo): void
    {
        $this->postScriptInfo = $postScriptInfo;
    }

    /**
     * @return BoundingBox
     */
    public function getBoundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    /**
     * @param BoundingBox $boundingBox
     */
    public function setBoundingBox(BoundingBox $boundingBox): void
    {
        $this->boundingBox = $boundingBox;
    }

    /**
     * @return GlyfTable
     */
    public function getGlyfTable(): GlyfTable
    {
        return $this->glyfTable;
    }

    /**
     * @param GlyfTable $glyfTable
     */
    public function setGlyfTable(GlyfTable $glyfTable): void
    {
        $this->glyfTable = $glyfTable;
    }

    /**
     * @return LongHorMetric
     */
    public function getLongHorMetric(): LongHorMetric
    {
        return $this->longHorMetric;
    }

    /**
     * @param LongHorMetric $longHorMetric
     */
    public function setLongHorMetric(LongHorMetric $longHorMetric): void
    {
        $this->longHorMetric = $longHorMetric;
    }
}
