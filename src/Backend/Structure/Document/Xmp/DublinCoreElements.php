<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Xmp;

/**
 * namespace http://purl.org/dc/elements/1.1/
 * see https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#section-3
 * see https://developer.adobe.com/xmp/docs/XMPNamespaces/dc/.
 */
readonly class DublinCoreElements
{
    public const DEFAULT_LANG = 'x-default';

    /**
     * @param array<string>         $language
     * @param array<string, string> $title
     * @param array<string, string> $description
     * @param string[]              $creators
     * @param string[]              $contributors
     * @param string[]              $publisher
     * @param string[]              $subject
     * @param string[]              $date
     */
    public function __construct(private array $language, private array $title, private array $description, private array $creators, private array $contributors, private array $publisher, private array $subject, private array $date)
    {
    }

    public static function createEmpty(): self
    {
        return new self([], [], [], [], [], [], [], []);
    }

    /**
     * @return string[]
     */
    public function getLanguage(): array
    {
        return $this->language;
    }

    /**
     * @return string[]
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @return string[]
     */
    public function getDescription(): array
    {
        return $this->description;
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
    public function getPublisher(): array
    {
        return $this->publisher;
    }

    /**
     * @return string[]
     */
    public function getSubject(): array
    {
        return $this->subject;
    }

    /**
     * @return string[]
     */
    public function getDate(): array
    {
        return $this->date;
    }
}
