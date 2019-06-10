<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2;

use PdfGenerator\IR\Structure2\Base\BaseStructure2;
use PdfGenerator\IR\Structure2\Content\ImagePlacement;
use PdfGenerator\IR\Structure2\Content\Rectangle;
use PdfGenerator\IR\Structure2\Content\Text;
use PdfGenerator\IR\Structure2Visitor;

class Page extends BaseStructure2
{
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var ImagePlacement[]
     */
    private $imagePlacements = [];

    /**
     * @var Text[]
     */
    private $texts = [];

    /**
     * @var Rectangle[]
     */
    private $rectangles = [];

    /**
     * Page constructor.
     *
     * @param int $pageNumber
     */
    public function __construct(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return ImagePlacement[]
     */
    public function getImagePlacements(): array
    {
        return $this->imagePlacements;
    }

    /**
     * @param ImagePlacement $imagePlacement
     */
    public function addImagePlacement(ImagePlacement $imagePlacement)
    {
        $this->imagePlacements[] = $imagePlacement;
    }

    /**
     * @return Text[]
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * @param Text $text
     */
    public function addText(Text $text)
    {
        $this->texts[] = $text;
    }

    /**
     * @return Rectangle[]
     */
    public function getRectangles(): array
    {
        return $this->rectangles;
    }

    /**
     * @param Rectangle $rectangle
     */
    public function addRectangle(Rectangle $rectangle)
    {
        $this->rectangles[] = $rectangle;
    }

    /**
     * @param Structure2Visitor $visitor
     *
     * @return mixed
     */
    public function accept(Structure2Visitor $visitor)
    {
        return $visitor->visitPage($this);
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->pageNumber;
    }
}
