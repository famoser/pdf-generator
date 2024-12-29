<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * keywords are used both as PDF keywords as well as Dublin Core subject
     *
     * @var string[]
     */
    private array $keywords = [];
    /**
     * @var string[]
     */
    private array $dates = [];

    /**
     * @param string[] $creators
     */
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
     * @param string[] $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @param string[] $dates
     */
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

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function getCreators(): array
    {
        return $this->creators;
    }

    /**
     * @return string[]
     */
    public function getContributors(): array
    {
        return $this->contributors;
    }

    /**
     * @return string[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }

    /**
     * @return string[]
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @return string[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    public function visit(DocumentVisitor $documentVisitor): XmpMeta
    {
        return $documentVisitor->visitMeta($this);
    }
}
