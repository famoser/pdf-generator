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

class FontSubsetDefinition
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
     * FontOptimizerPayload constructor.
     *
     * @param Character[] $characters
     * @param int[] $codePoints
     * @param int[] $missingCodePoints
     */
    public function __construct(array $characters, array $codePoints, array $missingCodePoints)
    {
        $this->characters = $characters;
        $this->codePoints = $codePoints;
        $this->missingCodePoints = $missingCodePoints;
    }

    /**
     * @return Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * @return int[]
     */
    public function getCodePoints(): array
    {
        return $this->codePoints;
    }

    /**
     * @return int[]
     */
    public function getMissingCodePoints(): array
    {
        return $this->missingCodePoints;
    }
}
