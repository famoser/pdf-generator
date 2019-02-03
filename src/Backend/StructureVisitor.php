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
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\Structure\Page;

class StructureVisitor
{
    /**
     * @var ContentVisitor
     */
    private $contentVisitor;

    /**
     * @var BaseObject[]
     */
    private $objectNodeLookup;

    /**
     * StructureVisitor constructor.
     */
    public function __construct()
    {
        $this->contentVisitor = new ContentVisitor();
    }

    /**
     * @param Structure\Catalog $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitCatalog(Structure\Catalog $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Catalog');

        $pagesElement = $structure->getPages()->accept($this, $file);

        $dictionary->addReferenceEntry('Pages', $pagesElement);

        return $dictionary;
    }

    /**
     * @param Structure\Pages $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitPages(Structure\Pages $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Pages');
        $this->objectNodeLookup[spl_object_id($structure)] = $dictionary;

        /** @var Page[] $kids */
        $kids = [];
        foreach ($structure->getKids() as $kid) {
            $kids[] = $kid->accept($this, $file);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }

    /**
     * @param Page $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitPage(Page $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Page');

        $parentReference = $this->objectNodeLookup[spl_object_id($structure->getParent())];
        $dictionary->addReferenceEntry('Parent', $parentReference);

        $resources = $structure->getResources()->accept($this, $file);
        $dictionary->addReferenceEntry('Resources', $resources);

        $dictionary->addNumberArrayEntry('MediaBox', $structure->getMediaBox());

        $contents = $structure->getContents()->accept($this, $file);
        $dictionary->addReferenceEntry('Contents', $contents);

        return $dictionary;
    }

    /**
     * @param Structure\Resources $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitResources(Structure\Resources $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();

        $fontDictionary = new DictionaryToken();
        foreach ($structure->getFonts() as $font) {
            $fontReference = $font->accept($this, $file);
            $fontDictionary->setEntry($font->getIdentifier(), new ReferenceToken($fontReference));
        }

        $dictionary->addDictionaryEntry('Font', $fontDictionary);

        return $dictionary;
    }

    /**
     * @param Structure\Contents $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitContents(Structure\Contents $structure, File $file): BaseObject
    {
        return $structure->getContent()->accept($this->contentVisitor, $file);
    }

    /**
     * @param Structure\Font $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitFont(Structure\Font $structure, File $file): BaseObject
    {
        $dictionary = $file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Font');
        $dictionary->addTextEntry('Subtype', $structure->getSubtype());
        $dictionary->addTextEntry('BaseFont', $structure->getBaseFont());

        return $dictionary;
    }
}
