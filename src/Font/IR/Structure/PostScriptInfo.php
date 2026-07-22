<?php

namespace Famoser\PdfGenerator\Font\IR\Structure;

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
