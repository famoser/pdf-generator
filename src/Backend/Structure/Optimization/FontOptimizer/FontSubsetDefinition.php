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

class FontSubsetDefinition
{
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
     * @param int[] $characterIndexToCodePointMapping
     * @param int[] $codePointsWithoutCharacter
     */
    public function __construct(array $characterIndexToCodePointMapping, array $codePointsWithoutCharacter)
    {
        $this->characterIndexToCodePointMapping = $characterIndexToCodePointMapping;
        $this->codePointsWithoutCharacter = $codePointsWithoutCharacter;
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
