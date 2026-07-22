<?php

namespace Famoser\PdfGenerator\Font\Frontend\File\Table;

/**
 * fallback table if an unknown table is encountered or one that is not serialized.
 */
class RawTable
{
    private string $tag;

    private string $content;

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
