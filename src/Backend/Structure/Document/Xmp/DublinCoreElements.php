<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Xmp;

/**
 * namespace http://purl.org/dc/elements/1.1/
 * see https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#section-3
 * see https://developer.adobe.com/xmp/docs/XMPNamespaces/dc/
 */
readonly class DublinCoreElements
{
    /**
     * @param array<string> $language
     * @param array<string, string> $title
     * @param array<string, string> $description
     * @param string[] $creators
     * @param string[] $contributors
     * @param string[] $publisher
     * @param string[] $subject
     */
    public function __construct(private array $language, private array $title, private array $description, private array $creators, private array $contributors, private array $publisher, private array $subject)
    {
    }

    public static function createEmpty(): self
    {
        return new self([], [], [], [], [], [], []);
    }

    public function getLanguage(): array
    {
        return $this->language;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getCreators(): array
    {
        return $this->creators;
    }

    public function getContributors(): array
    {
        return $this->contributors;
    }

    public function getPublisher(): array
    {
        return $this->publisher;
    }

    public function getSubject(): array
    {
        return $this->subject;
    }
}
