<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Structure\File;

class ContentVisitor
{
    /**
     * @param Content\TextContent $param
     * @param File $file
     *
     * @return \Pdf\Backend\Object\StreamObject
     */
    public function visitTextContent(Content\TextContent $param, File $file): BaseObject
    {
        // BT: begin text
        $content = 'BT ';

        // set font & font size with Tf function
        $content .= '/' . $param->getFont()->getIdentifier() . ' ' . $param->getFontSize() . ' Tf ';

        // set x/y coordinate with Td function
        $content .= $param->getXCoordinate() . ' ' . $param->getYCoordinate() . ' Td';

        // print text with Tj
        $content .= '(' . $param->getText() . ')Tj ';

        // ET: end text
        $content .= 'ET';

        return $file->addStreamObject($content);
    }
}
