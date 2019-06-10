<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font;

use PdfGenerator\Backend\Structure\Document\Font\CharacterMapping;
use PdfGenerator\Backend\Structure\DocumentVisitor;
use PdfGenerator\Backend\Structure\Font;

class EmbeddedFont extends Font
{
    /**
     * @var string
     */
    private $fontContent;

    /**
     * @var CharacterMapping[]
     */
    private $characterMappings;

    /**
     * @var int[]
     */
    private $characterWidths;

    /**
     * EmbeddedFont constructor.
     *
     * @param string $baseFont
     * @param string $fontContent
     * @param CharacterMapping[] $characterMappings
     * @param array $characterWidths
     */
    public function __construct(string $baseFont, string $fontContent, array $characterMappings, array $characterWidths)
    {
        parent::__construct($baseFont);

        $this->fontContent = $fontContent;
        $this->characterMappings = $characterMappings;
        $this->characterWidths = $characterWidths;
    }

    /**
     * @return string
     */
    public function getFontContent(): string
    {
        return $this->fontContent;
    }

    /**
     * @return CharacterMapping[]
     */
    public function getCharacterMappings(): array
    {
        return $this->characterMappings;
    }

    /**
     * @return int[]
     */
    public function getCharacterWidths(): array
    {
        return $this->characterWidths;
    }

    /**
     * @param DocumentVisitor $documentVisitor
     *
     * @return mixed
     */
    public function accept(DocumentVisitor $documentVisitor)
    {
        return $documentVisitor->visitEmbeddedFont($this);
    }
}
