<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Optimization\FontOptimizer;

use PdfGenerator\Font\IR\Structure\Character;

class FontOptimizerPayload
{
    /**
     * @var Character[]
     */
    private $characters;

    /**
     * @var int[]
     */
    private $codePoints;

    /**
     * @var int[]
     */
    private $missingCodePoints;

    /**
     * @return Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * @param Character[] $characters
     */
    public function setCharacters(array $characters): void
    {
        $this->characters = $characters;
    }

    /**
     * @return int[]
     */
    public function getCodePoints(): array
    {
        return $this->codePoints;
    }

    /**
     * @param int[] $codePoints
     */
    public function setCodePoints(array $codePoints): void
    {
        $this->codePoints = $codePoints;
    }

    /**
     * @return int[]
     */
    public function getMissingCodePoints(): array
    {
        return $this->missingCodePoints;
    }

    /**
     * @param int[] $missingCodePoints
     */
    public function setMissingCodePoints(array $missingCodePoints): void
    {
        $this->missingCodePoints = $missingCodePoints;
    }
}
