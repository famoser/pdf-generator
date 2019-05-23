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

use SplFileObject;

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
        if ($this->aGLFMappingCache === null) {
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
        if ($this->macintoshMappingCache === null) {
            $this->macintoshMappingCache = $this->generateMacintoshMapping();
        }

        return $this->macintoshMappingCache;
    }

    /**
     * @return string[]
     */
    private function generateAGLFMapping(): array
    {
        $path = __DIR__ . \DIRECTORY_SEPARATOR . 'aglfn.txt';

        $result = [];

        $file = new SplFileObject($path);
        while (!$file->eof()) {
            $line = $file->getCurrentLine();

            // ensure line could be read out
            if ($line === false) {
                break;
            }

            // ignore comments
            if (substr($line, 0, 1) === '#') {
                continue;
            }

            $content = explode(';', $line);

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
        $path = __DIR__ . \DIRECTORY_SEPARATOR . 'macintosh_standard_ordering.txt';

        $result = [];

        $file = new SplFileObject($path);
        while (!$file->eof()) {
            $line = $file->getCurrentLine();

            // ensure line could be read out
            if ($line === false) {
                break;
            }

            $content = explode("\t", $line);

            $macintoshPoint = (int)trim($content[0]);
            $result[$macintoshPoint] = trim($content[1]);
        }

        return $result;
    }
}
