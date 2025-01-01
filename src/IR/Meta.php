<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR;

use Famoser\PdfGenerator\Backend\Structure\Document\XmpMeta;

readonly class Meta
{
    /**
     * @param string[] $otherLanguages
     * @param string[] $titleTranslations
     * @param string[] $descriptionTranslations
     * @param string[] $creators
     * @param string[] $contributors
     * @param string[] $publishers
     * @param string[] $keywords                keywords are used both as PDF keywords as well as Dublin Core subject
     * @param string[] $dates
     */
    public function __construct(private ?string $language, private array $otherLanguages, private ?string $title, private array $titleTranslations, private ?string $description, private array $descriptionTranslations, private array $creators, private array $contributors, private array $publishers, private array $keywords, private array $dates)
    {
    }

    public static function empty(): self
    {
        return new self(null, [], null, [], null, [], [], [], [], [], []);
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

    public function accept(DocumentVisitor $documentVisitor): XmpMeta
    {
        return $documentVisitor->visitMeta($this);
    }
}
