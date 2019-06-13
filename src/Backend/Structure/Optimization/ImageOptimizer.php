<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Optimization;

class ImageOptimizer
{
    /**
     * @param string $imageContent
     * @param int $targetWidth
     * @param int $targetHeight
     *
     * @return string
     */
    public function transformToJpgAndResize(string $imageContent, int $targetWidth, int $targetHeight)
    {
        $originalImage = imagecreatefromstring($imageContent);
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($originalImage), imagesy($originalImage));

        return $this->catchOutput(function () use ($newImage) {
            imagejpeg($newImage, null, 90);
        });
    }

    /**
     * @param callable $func
     *
     * @return string
     */
    private function catchOutput(callable $func)
    {
        ob_start();
        $func();
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
