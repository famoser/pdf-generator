<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend;

use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\File\Object\StreamObject;

class ContentVisitor
{
    /**
     * @param Content\TextContent $param
     * @param File $file
     *
     * @return \PdfGenerator\Backend\File\Object\StreamObject
     */
    public function visitTextContent(Content\TextContent $param, File $file): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'BT';

        // set font & font size with Tf function
        $content[] = '/' . $param->getFont()->getIdentifier() . ' ' . $param->getFontSize() . ' Tf';

        // set x/y coordinate with Td function
        $content[] = $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' Td';

        // print text with Tj
        $content[] = '(' . $param->getText() . ')Tj';

        // ET: end text
        $content[] = 'ET';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_TEXT);
    }

    /**
     * @param Content\ImageContent $param
     * @param File $file
     *
     * @return \PdfGenerator\Backend\File\Object\StreamObject
     */
    public function visitImageContent(Content\ImageContent $param, File $file): BaseObject
    {
        $content = [];

        // BT: begin text
        $content[] = 'q';

        // scale by 132 and translate to x/y
        $content[] = $param->getWidth() . ' 0 0 ' . $param->getHeight() . ' ' . $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' cm';

        // set font & font size with Tf function
        $content[] = '/' . $param->getImage()->getIdentifier() . ' Do';

        // ET: end text
        $content[] = 'Q';

        return $file->addStreamObject(implode(' ', $content), StreamObject::CONTENT_TYPE_TEXT);
    }
}
