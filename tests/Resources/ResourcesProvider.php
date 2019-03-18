<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Resources;

class ResourcesProvider
{
    /**
     * @return bool|string
     */
    public static function getImage1Path()
    {
        return realpath(__DIR__ . \DIRECTORY_SEPARATOR . 'images' . \DIRECTORY_SEPARATOR . 'image1.jpg');
    }
}
