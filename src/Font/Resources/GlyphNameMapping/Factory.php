<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Resources\GlyphNameMapping;

class Factory
{
    /**
     * @var string[]|null
     */
    private ?array $aGLFMappingCache = null;

    /**
     * @return string[]
     */
    public function getAGLFMapping(): array
    {
        if (null === $this->aGLFMappingCache) {
            $this->aGLFMappingCache = $this->generateAGLFMapping();
        }

        return $this->aGLFMappingCache;
    }

    /**
     * @var string[]|null
     */
    private ?array $macintoshMappingCache = null;

    /**
     * @return string[]
     */
    public function getMacintoshMapping(): array
    {
        if (null === $this->macintoshMappingCache) {
            $this->macintoshMappingCache = $this->generateMacintoshMapping();
        }

        return $this->macintoshMappingCache;
    }

    /**
     * @return string[]
     */
    private function generateAGLFMapping(): array
    {
        $path = __DIR__.\DIRECTORY_SEPARATOR.'aglfn.txt';

        $result = [];

        $file = new \SplFileObject($path);
        while (!$file->eof()) {
            $line = $file->getCurrentLine();

            // ignore comments or empty lines
            if (str_starts_with($line, '#') || 0 === \strlen($line)) {
                continue;
            }

            $content = explode(';', $line);
            if (\count($content) < 2) {
                continue;
            }

            $codePoint = hexdec($content[0]);
            $result[$codePoint] = $content[1];
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private function generateMacintoshMapping(): array
    {
        $path = __DIR__.\DIRECTORY_SEPARATOR.'macintosh_standard_ordering.txt';

        $result = [];

        $file = new \SplFileObject($path);
        while (!$file->eof()) {
            $line = $file->getCurrentLine();

            $content = explode("\t", $line);

            $macintoshPoint = (int) trim($content[0]);
            $result[$macintoshPoint] = trim($content[1]);
        }

        return $result;
    }
}
