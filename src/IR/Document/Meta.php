<?php

namespace Famoser\PdfGenerator\IR\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;
use Famoser\PdfGenerator\IR\DocumentVisitor;

/**
 * see https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#section-3 for details on how to use the fields
 */
class Meta
{
    private ?string $language = null;
    /**
     * @var string[]
     */
    private array $otherLanguages = [];

    private ?string $title = null;
    /**
     * @var array<string, string>
     */
    private array $titleTranslations = [];
    private ?string $description = null;
    /**
     * @var array<string, string>
     */
    private array $descriptionTranslations = [];

    /**
     * @var string[]
     */
    private array $creators = [];

    /**
     * @var string[]
     */
    private array $contributors = [];
    /**
     * @var string[]
     */
    private array $publishers = [];
    /**
     * @var string[]
     */
    private array $subjects = [];
    /**
     * @var string[]
     */
    private array $dates = [];

    public static function createMeta(string $language, string $title, array $creators): self
    {
        $meta = new self();
        $meta->language = $language;
        $meta->title = $title;
        $meta->creators = $creators;

        return $meta;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function addOtherLanguages(string $language): void
    {
        $this->otherLanguages[] = $language;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function addTitleTranslation(string $language, string $title): void
    {
        $this->titleTranslations[$language] = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function addDescriptionTranslation(string $language, string $description): void
    {
        $this->descriptionTranslations[$language] = $description;
    }

    /**
     * @param string[] $creators
     */
    public function setCreators(array $creators): void
    {
        $this->creators = $creators;
    }

    /**
     * @param string[] $contributors
     */
    public function setContributors(array $contributors): void
    {
        $this->contributors = $contributors;
    }

    /**
     * @param string[] $publishers
     */
    public function setPublishers(array $publishers): void
    {
        $this->publishers = $publishers;
    }

    public function setSinglePublisher(string $publisher): void
    {
        $this->publishers = [$publisher];
    }

    /**
     * @param string[] $subjects
     */
    public function setSubjects(array $subjects): void
    {
        $this->subjects = $subjects;
    }

    /**
     * @param string[] $keywords
     */
    public function setKeywordSubjects(array $keywords): void
    {
        $this->subjects = $keywords;
    }

    public function setDates(array $dates): void
    {
        $this->dates = $dates;
    }

    public function setSingleDate(\DateTimeInterface $date): void
    {
        $this->dates = [$date->format('Y-m-d')];
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getOtherLanguages(): array
    {
        return $this->otherLanguages;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return array<string, string>
     */
    public function getTitleTranslations(): array
    {
        return $this->titleTranslations;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array<string, string>
     */
    public function getDescriptionTranslations(): array
    {
        return $this->descriptionTranslations;
    }

    public function getCreators(): array
    {
        return $this->creators;
    }

    public function getContributors(): array
    {
        return $this->contributors;
    }

    public function getPublishers(): array
    {
        return $this->publishers;
    }

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    public function getDates(): array
    {
        return $this->dates;
    }

    public function visit(DocumentVisitor $documentVisitor): XmpMeta
    {
        return $documentVisitor->visitMeta($this);
    }
}
