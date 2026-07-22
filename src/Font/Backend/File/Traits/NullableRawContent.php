<?php

namespace Famoser\PdfGenerator\Font\Backend\File\Traits;

trait NullableRawContent
{
    /**
     * raw byte data.
     */
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
