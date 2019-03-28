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
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\Structure\Base\IdentifiableStructure;
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
        $dictionary->addReferenceArrayEntry('Contents', $contents);

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

        // see PDF320000_2008 14.2
        $procSet = ['PDF'];

        if (\count($structure->getFonts()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getFonts(), $file);
            $dictionary->addDictionaryEntry('Font', $fontDictionary);
            $procSet[] = 'Text';
        }

        if (\count($structure->getImages()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getImages(), $file);
            $dictionary->addDictionaryEntry('XObject', $fontDictionary);
            $procSet[] = 'ImageC';
        }

        $dictionary->addTextArrayEntry('ProcSet', $procSet, '/');

        return $dictionary;
    }

    /**
     * @param IdentifiableStructure[] $structures
     * @param File $file
     *
     * @return DictionaryToken
     */
    private function createReferenceDictionary(array $structures, File $file)
    {
        $dictionary = new DictionaryToken();
        foreach ($structures as $structure) {
            $reference = $structure->accept($this, $file);
            $dictionary->setEntry($structure->getIdentifier(), new ReferenceToken($reference));
        }

        return $dictionary;
    }

    /**
     * @param Structure\Contents $structure
     * @param File $file
     *
     * @return BaseObject[]
     */
    public function visitContents(Structure\Contents $structure, File $file): array
    {
        /** @var BaseObject[] $baseObjects */
        $baseObjects = [];

        foreach ($structure->getContent() as $baseContent) {
            $baseObjects[] = $baseContent->accept($this->contentVisitor, $file);
        }

        return $baseObjects;
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
        $dictionary->addTextEntry('Type', '/Font');
        $dictionary->addTextEntry('Subtype', $structure->getSubtype());
        $dictionary->addTextEntry('BaseFont', $structure->getBaseFont());

        return $dictionary;
    }

    /**
     * @param Structure\Image $structure
     * @param File $file
     *
     * @return BaseObject
     */
    public function visitImage(Structure\Image $structure, File $file): BaseObject
    {
        $stream = $file->addStreamObject($structure->getImageData(), StreamObject::CONTENT_TYPE_IMAGE);

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Type', '/XObject');
        $dictionary->setTextEntry('Subtype', '/Image');
        $dictionary->setTextEntry('Width', $structure->getWidth());
        $dictionary->setTextEntry('Height', $structure->getHeight());
        $dictionary->setTextEntry('Filter', $structure->getFilter());
        $dictionary->setTextEntry('BitsPerComponent', 8);
        $dictionary->setTextEntry('ColorSpace', '/DeviceRGB');
        $dictionary->setTextEntry('Filter', '/DCTDecode');

        return $stream;
    }
}
