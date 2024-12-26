<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Optimization;

class ImageOptimizer
{
    public function transformToJpgAndResize(string $imageContent, int $targetWidth, int $targetHeight): string
    {
        $originalImage = imagecreatefromstring($imageContent);
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // if construction fails; do not change anything
        if (!$originalImage || !$newImage) {
            return $imageContent;
        }

        imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($originalImage), imagesy($originalImage));

        return $this->catchOutput(function () use ($newImage) {
            imagejpeg($newImage, null, 90);
        });
    }

    /**
     * @return int[]
     */
    public function getTargetHeightWidth(int $width, int $height, int $maxWidth, int $maxHeight, int $dpi): array
    {
        $maxWidth = $maxWidth * $dpi;
        $maxHeight = $maxHeight * $dpi;

        // if wider than needed, resize such that width = maxWidth
        if ($width > $maxWidth) {
            $smallerBy = $maxWidth / (float) $width;
            $width = $maxWidth;
            $height = $height * $smallerBy;
        }

        // if height is lower, resize such that height = maxHeight
        if ($height < $maxHeight) {
            $biggerBy = $maxHeight / (float) $height;
            $height = $maxHeight;
            $width = $width * $biggerBy;
        }

        return [$width, $height];
    }

    private function catchOutput(callable $func): string
    {
        ob_start();
        $func();
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
