<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Traits;

use PdfGenerator\Font\Frontend\StreamReader;

class Reader
{
    /**
     * @throws \Exception
     */
    public static function readBinaryTreeSearchableUInt16(StreamReader $fileReader, BinaryTreeSearchableTrait $binaryTreeSearchable): void
    {
        $binaryTreeSearchable->setSearchRange($fileReader->readUInt16());
        $binaryTreeSearchable->setEntrySelector($fileReader->readUInt16());
        $binaryTreeSearchable->setRangeShift($fileReader->readUInt16());
    }

    /**
     * @throws \Exception
     */
    public static function readBoundingBoxInt16(StreamReader $fileReader, BoundingBoxTrait $boundingBoxTrait): void
    {
        $boundingBoxTrait->setXMin($fileReader->readInt16());
        $boundingBoxTrait->setYMin($fileReader->readInt16());
        $boundingBoxTrait->setXMax($fileReader->readInt16());
        $boundingBoxTrait->setYMax($fileReader->readInt16());
    }

    /**
     * @throws \Exception
     */
    public static function readBoundingBoxFWORD(StreamReader $fileReader, BoundingBoxTrait $boundingBoxTrait): void
    {
        $boundingBoxTrait->setXMin($fileReader->readFWORD());
        $boundingBoxTrait->setYMin($fileReader->readFWORD());
        $boundingBoxTrait->setXMax($fileReader->readFWORD());
        $boundingBoxTrait->setYMax($fileReader->readFWORD());
    }
}
