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
     * @param string $sourcePath
     * @param int $targetWidth
     * @param int $targetHeight
     *
     * @return string
     */
    public function resize(string $sourcePath, int $targetWidth, int $targetHeight)
    {
        $ending = pathinfo($sourcePath, PATHINFO_EXTENSION);

        //resize & save
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($ending === 'jpg' || $ending === 'jpeg') {
            $originalImage = imagecreatefromjpeg($sourcePath);
            imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($originalImage), imagesy($originalImage));

            return $this->catchOutput(function () use ($newImage) {
                imagejpeg($newImage, null, 90);
            });
        } elseif ($ending === 'png') {
            $originalImage = imagecreatefrompng($sourcePath);
            imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($originalImage), imagesy($originalImage));

            return $this->catchOutput(function () use ($newImage) {
                imagepng($newImage, null, 9);
            });
        } elseif ($ending === 'gif') {
            $originalImage = imagecreatefromgif($sourcePath);
            imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($originalImage), imagesy($originalImage));

            return $this->catchOutput(function () use ($newImage) {
                imagegif($newImage);
            });
        } else {
            return file_get_contents($sourcePath);
        }
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
