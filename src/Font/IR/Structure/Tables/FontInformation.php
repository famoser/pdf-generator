<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure\Tables;

class FontInformation
{
    /**
     * @var string|null
     */
    private $copyrightNotice;

    /**
     * the family of the font; used to group together fonts with different style/width.
     *
     * note that only four variations are allowed (regular, italic, bold, bold italic).
     * if you need more variations, use the typographicFamily & typographicSubfamilyName fields.
     *
     * @var string|null
     */
    private $family;

    /**
     * the variation of the specific font family (one of regular, italic, bold or bold italic).
     *
     * @var string|null
     */
    private $subfamily;

    /**
     * unique font identifier.
     *
     * @var string|null
     */
    private $identifier;

    /**
     * human readable name of the font typically consisting of the family / subfamily.
     *
     * @var string|null
     */
    private $fullName;

    /**
     * to compare font versions.
     *
     * should be of the form "Version 1.1"
     *
     * @var string|null
     */
    private $version;

    /**
     * the name of the font for PostScript when invoking `composefont`.
     *
     * at most 63 chars, ASCII subset, codes 33 through 126, except '[', ']', '(', ')', '{', '}', '<', '>', '/', '%'.
     *
     * @var string|null
     */
    private $postScriptName;

    /**
     * trademark information for the font (should be based on legal advice).
     *
     * @var string|null
     */
    private $trademarkNotice;

    /**
     * name of the manufacturer.
     *
     * @var string|null
     */
    private $manufacturer;

    /**
     * name of the designer.
     *
     * @var string|null
     */
    private $designer;

    /**
     * description of the typeface (history, usage recommendations, ...).
     *
     * @var string|null
     */
    private $description;

    /**
     * url of font vendor.
     *
     * @var string|null
     */
    private $urlVendor;

    /**
     * url of font designer.
     *
     * @var string|null
     */
    private $urlDesigner;

    /**
     * plain english description of how the font may be used.
     *
     * @var string|null
     */
    private $licenseDescription;

    /**
     * url of the license.
     *
     * @var string|null
     */
    private $licenseUrl;

    /**
     * the family of the font; used to group together fonts with different style/width.
     *
     * @var string|null
     */
    private $typographicFamily;

    /**
     * the variation of the specific font family (one of regular, italic, bold or bold italic).
     *
     * @var string|null
     */
    private $typographicSubfamily;

    /**
     * how the font should be called on Macintosh.
     *
     * @var string|null
     */
    private $compatibleFull;

    /**
     * the text best to sample the font by.
     *
     * @var string|null
     */
    private $sampleText;

    /**
     * the name of the font for PostScript when invoking `findfont`.
     *
     * ASCII subset, codes 33 through 126, except '[', ']', '(', ')', '{', '}', '<', '>', '/', '%'.
     *
     * @var string|null
     */
    private $postScriptCIDName;

    /**
     * the family of the font conforming to WWS (width, weight, slope) if the typographic family includes additional specifiers (like "Display", "Body").
     *
     * if there are two fonts called "Calibri Body" and "Calibri Header" then their wws name would be for both "Calibri"
     *
     * @var string|null
     */
    private $wwsFamilyName;

    /**
     * the Subfamily of the font conforming to WWS (width, weight, slope) if the typographic subfamily includes additional specifiers (like "Display", "Body").
     *
     * @var string|null
     */
    private $wwsSubfamilyName;

    public function getCopyrightNotice(): ?string
    {
        return $this->copyrightNotice;
    }

    public function setCopyrightNotice(?string $copyrightNotice): void
    {
        $this->copyrightNotice = $copyrightNotice;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): void
    {
        $this->family = $family;
    }

    public function getSubfamily(): ?string
    {
        return $this->subfamily;
    }

    public function setSubfamily(?string $subfamily): void
    {
        $this->subfamily = $subfamily;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    public function getPostScriptName(): ?string
    {
        return $this->postScriptName;
    }

    public function setPostScriptName(?string $postScriptName): void
    {
        $this->postScriptName = $postScriptName;
    }

    public function getTrademarkNotice(): ?string
    {
        return $this->trademarkNotice;
    }

    public function setTrademarkNotice(?string $trademarkNotice): void
    {
        $this->trademarkNotice = $trademarkNotice;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getDesigner(): ?string
    {
        return $this->designer;
    }

    public function setDesigner(?string $designer): void
    {
        $this->designer = $designer;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getUrlVendor(): ?string
    {
        return $this->urlVendor;
    }

    public function setUrlVendor(?string $urlVendor): void
    {
        $this->urlVendor = $urlVendor;
    }

    public function getUrlDesigner(): ?string
    {
        return $this->urlDesigner;
    }

    public function setUrlDesigner(?string $urlDesigner): void
    {
        $this->urlDesigner = $urlDesigner;
    }

    public function getLicenseDescription(): ?string
    {
        return $this->licenseDescription;
    }

    public function setLicenseDescription(?string $licenseDescription): void
    {
        $this->licenseDescription = $licenseDescription;
    }

    public function getLicenseUrl(): ?string
    {
        return $this->licenseUrl;
    }

    public function setLicenseUrl(?string $licenseUrl): void
    {
        $this->licenseUrl = $licenseUrl;
    }

    public function getTypographicFamily(): ?string
    {
        return $this->typographicFamily;
    }

    public function setTypographicFamily(?string $typographicFamily): void
    {
        $this->typographicFamily = $typographicFamily;
    }

    public function getTypographicSubfamily(): ?string
    {
        return $this->typographicSubfamily;
    }

    public function setTypographicSubfamily(?string $typographicSubfamily): void
    {
        $this->typographicSubfamily = $typographicSubfamily;
    }

    public function getCompatibleFull(): ?string
    {
        return $this->compatibleFull;
    }

    public function setCompatibleFull(?string $compatibleFull): void
    {
        $this->compatibleFull = $compatibleFull;
    }

    public function getSampleText(): ?string
    {
        return $this->sampleText;
    }

    public function setSampleText(?string $sampleText): void
    {
        $this->sampleText = $sampleText;
    }

    public function getPostScriptCIDName(): ?string
    {
        return $this->postScriptCIDName;
    }

    public function setPostScriptCIDName(?string $postScriptCIDName): void
    {
        $this->postScriptCIDName = $postScriptCIDName;
    }

    public function getWwsFamilyName(): ?string
    {
        return $this->wwsFamilyName;
    }

    public function setWwsFamilyName(?string $wwsFamilyName): void
    {
        $this->wwsFamilyName = $wwsFamilyName;
    }

    public function getWwsSubfamilyName(): ?string
    {
        return $this->wwsSubfamilyName;
    }

    public function setWwsSubfamilyName(?string $wwsSubfamilyName): void
    {
        $this->wwsSubfamilyName = $wwsSubfamilyName;
    }
}
