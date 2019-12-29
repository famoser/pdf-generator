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
    private $characterIndexToCodePointMapping;

    /**
     * @var int[]
     */
    private $codePointsWithoutCharacter;

    /**
     * FontOptimizerPayload constructor.
     *
     * @param Character[] $characters
     * @param int[] $characterIndexToCodePointMapping
     * @param int[] $codePointsWithoutCharacter
     */
    public function __construct(array $characters, array $characterIndexToCodePointMapping, array $codePointsWithoutCharacter)
    {
        $this->characters = $characters;
        $this->characterIndexToCodePointMapping = $characterIndexToCodePointMapping;
        $this->codePointsWithoutCharacter = $codePointsWithoutCharacter;
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
    public function getCharacterIndexToCodePointMapping(): array
    {
        return $this->characterIndexToCodePointMapping;
    }

    /**
     * @return int[]
     */
    public function getCodePointsWithoutCharacter(): array
    {
        return $this->codePointsWithoutCharacter;
    }
}
