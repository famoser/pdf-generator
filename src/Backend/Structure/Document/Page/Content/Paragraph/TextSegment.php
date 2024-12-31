<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\WritingState;

readonly class TextSegment
{
    public function __construct(private string $text, private WritingState $writingState)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->writingState->getState();
    }

    public function getTextState(): TextState
    {
        return $this->writingState->getTextState();
    }
}
