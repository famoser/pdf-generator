<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Traits;

use PdfGenerator\Font\Backend\StreamWriter;

class Writer
{
    /**
     * @param BinaryTreeSearchableTrait $binaryTreeSearchable
     */
    public static function writeBinaryTreeSearchableUInt16(mixed $binaryTreeSearchable, StreamWriter $writer): void
    {
        $writer->writeUInt16($binaryTreeSearchable->getSearchRange());
        $writer->writeUInt16($binaryTreeSearchable->getEntrySelector());
        $writer->writeUInt16($binaryTreeSearchable->getRangeShift());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     */
    public static function writeBoundingBoxInt16(mixed $boundingBoxTrait, StreamWriter $writer): void
    {
        $writer->writeInt16($boundingBoxTrait->getXMin());
        $writer->writeInt16($boundingBoxTrait->getYMin());
        $writer->writeInt16($boundingBoxTrait->getXMax());
        $writer->writeInt16($boundingBoxTrait->getYMax());
    }

    /**
     * @param BoundingBoxTrait $boundingBoxTrait
     */
    public static function writeBoundingBoxFWORD(mixed $boundingBoxTrait, StreamWriter $writer): void
    {
        $writer->writeFWORD($boundingBoxTrait->getXMin());
        $writer->writeFWORD($boundingBoxTrait->getYMin());
        $writer->writeFWORD($boundingBoxTrait->getXMax());
        $writer->writeFWORD($boundingBoxTrait->getYMax());
    }
}
