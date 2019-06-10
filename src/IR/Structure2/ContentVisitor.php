<?php


namespace PdfGenerator\IR\Structure2\Content;


use PdfGenerator\Backend\Content\ImageContent;
use PdfGenerator\IR\Configuration\Level\PageLevelRepository;
use PdfGenerator\IR\Configuration\State\GeneralGraphicStateRepository;

class ContentVisitor
{
    /**
     * @var GeneralGraphicStateRepository
     */
    private $generalGraphicStateRepository;


    public function visitImagePlacement(ImagePlacement $param)
    {
        $image = new ImageContent($param->getImage(), )
    }
}
