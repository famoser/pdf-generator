<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\TextWriter;

class TextBlock
{
    public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';
    public const ALIGNMENT_RIGHT = 'ALIGNMENT_RIGHT';
    public const ALIGNMENT_CENTER = 'ALIGNMENT_CENTER';

    /**
     * @var string
     */
    private $alignment = self::ALIGNMENT_LEFT;

    /**
     * @var MeasuredPhrase[]
     */
    private $measuredPhrases;

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): void
    {
        $this->alignment = $alignment;
    }

    public function addMeasuredPhrase(MeasuredPhrase $measuredPhrase)
    {
        $this->measuredPhrases[] = $measuredPhrase;
    }

    /**
     * @return MeasuredPhrase[]
     */
    public function getMeasuredPhrases(): array
    {
        return $this->measuredPhrases;
    }
}
