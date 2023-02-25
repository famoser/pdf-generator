<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Resources\GlyphNameMapping;

class Factory
{
    /**
     * @var string[]
     */
    private $aGLFMappingCache;

    /**
     * @return string[]
     */
    public function getAGLFMapping()
    {
        if (null === $this->aGLFMappingCache) {
            $this->aGLFMappingCache = $this->generateAGLFMapping();
        }

        return $this->aGLFMappingCache;
    }

    /**
     * @var string[]
     */
    private $macintoshMappingCache;

    /**
     * @return string[]
     */
    public function getMacintoshMapping()
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

            // ensure line could be read out
            if (false === $line) {
                break;
            }

            // ignore comments or empty lines
            if ('#' === substr($line, 0, 1) || 0 === \strlen($line)) {
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

            // ensure line could be read out
            if (false === $line) {
                break;
            }

            $content = explode("\t", $line);

            $macintoshPoint = (int) trim($content[0]);
            $result[$macintoshPoint] = trim($content[1]);
        }

        return $result;
    }
}
