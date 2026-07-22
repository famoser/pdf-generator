<?php

namespace Famoser\PdfGenerator\Font\IR\Utils\Post;

class GlyphInfo
{
    private ?int $macintoshIndex = null;

    private ?string $name = null;

    public function getMacintoshIndex(): ?int
    {
        return $this->macintoshIndex;
    }

    public function setMacintoshIndex(?int $macintoshIndex): void
    {
        $this->macintoshIndex = $macintoshIndex;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
