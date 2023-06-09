<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure;

class PostScriptInfo
{
    private ?int $macintoshGlyphIndex = null;

    private string $name;

    public function isInStandardMacintoshSet(): bool
    {
        return null !== $this->macintoshGlyphIndex;
    }

    public function getMacintoshGlyphIndex(): ?int
    {
        return $this->macintoshGlyphIndex;
    }

    public function setMacintoshGlyphIndex(?int $macintoshGlyphIndex): void
    {
        $this->macintoshGlyphIndex = $macintoshGlyphIndex;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
