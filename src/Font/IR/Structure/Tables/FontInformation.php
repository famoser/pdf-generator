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
    private ?string $copyrightNotice;

    /**
     * the family of the font; used to group together fonts with different style/width.
     *
     * note that only four variations are allowed (regular, italic, bold, bold italic).
     * if you need more variations, use the typographicFamily & typographicSubfamilyName fields.
     */
    private ?string $family;

    /**
     * the variation of the specific font family (one of regular, italic, bold or bold italic).
     */
    private ?string $subfamily;

    /**
     * unique font identifier.
     */
    private ?string $identifier;

    /**
     * human readable name of the font typically consisting of the family / subfamily.
     */
    private ?string $fullName;

    /**
     * to compare font versions.
     *
     * should be of the form "Version 1.1"
     */
    private ?string $version;

    /**
     * the name of the font for PostScript when invoking `composefont`.
     *
     * at most 63 chars, ASCII subset, codes 33 through 126, except '[', ']', '(', ')', '{', '}', '<', '>', '/', '%'.
     */
    private ?string $postScriptName;

    /**
     * trademark information for the font (should be based on legal advice).
     */
    private ?string $trademarkNotice;

    /**
     * name of the manufacturer.
     */
    private ?string $manufacturer;

    /**
     * name of the designer.
     */
    private ?string $designer;

    /**
     * description of the typeface (history, usage recommendations, ...).
     */
    private ?string $description;

    /**
     * url of font vendor.
     */
    private ?string $urlVendor;

    /**
     * url of font designer.
     */
    private ?string $urlDesigner;

    /**
     * plain english description of how the font may be used.
     */
    private ?string $licenseDescription;

    /**
     * url of the license.
     */
    private ?string $licenseUrl;

    /**
     * the family of the font; used to group together fonts with different style/width.
     */
    private ?string $typographicFamily;

    /**
     * the variation of the specific font family (one of regular, italic, bold or bold italic).
     */
    private ?string $typographicSubfamily;

    /**
     * how the font should be called on Macintosh.
     */
    private ?string $compatibleFull;

    /**
     * the text best to sample the font by.
     */
    private ?string $sampleText;

    /**
     * the name of the font for PostScript when invoking `findfont`.
     *
     * ASCII subset, codes 33 through 126, except '[', ']', '(', ')', '{', '}', '<', '>', '/', '%'.
     */
    private ?string $postScriptCIDName;

    /**
     * the family of the font conforming to WWS (width, weight, slope) if the typographic family includes additional specifiers (like "Display", "Body").
     *
     * if there are two fonts called "Calibri Body" and "Calibri Header" then their wws name would be for both "Calibri"
     */
    private ?string $wwsFamilyName;

    /**
     * the Subfamily of the font conforming to WWS (width, weight, slope) if the typographic subfamily includes additional specifiers (like "Display", "Body").
     */
    private ?string $wwsSubfamilyName;

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
