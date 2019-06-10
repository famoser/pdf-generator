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
use PdfGenerator\Backend\File\Object\DictionaryObject;
use PdfGenerator\Backend\File\Object\StreamObject;
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\Structure\Base\IdentifiableStructureTrait;
use PdfGenerator\Backend\Structure\ContentVisitor;
use PdfGenerator\Backend\Structure\Font\Structure\CIDSystemInfo;
use PdfGenerator\Backend\Structure\Font\Structure\CMap;
use PdfGenerator\Backend\Structure\Font\Structure\FontDescriptor;
use PdfGenerator\Backend\Structure\Page;

class StructureVisitor
{
    /**
     * @var BaseObject[]
     */
    private $objectNodeLookup;

    /**
     * @var File
     */
    private $file;

    /**
     * StructureVisitor constructor.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param Structure\Catalog $structure
     *
     * @return BaseObject
     */
    public function visitCatalog(Structure\Catalog $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Catalog');

        $pagesDictionary = $this->createReferenceDictionary($structure->getPages());
        $dictionary->addDictionaryEntry('Pages', $pagesDictionary);

        return $dictionary;
    }

    /**
     * @param Structure\Pages $structure
     *
     * @return BaseObject
     */
    public function visitPages(Structure\Pages $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Pages');
        $this->objectNodeLookup[spl_object_id($structure)] = $dictionary;

        /** @var Page[] $kids */
        $kids = [];
        foreach ($structure->getKids() as $kid) {
            $kids[] = $kid->accept($this);
        }

        $dictionary->addReferenceArrayEntry('Kids', $kids);
        $dictionary->addNumberEntry('Count', \count($kids));

        return $dictionary;
    }

    /**
     * @param Page $structure
     *
     * @return BaseObject
     */
    public function visitPage(Page $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();
        $dictionary->addTextEntry('Type', 'Page');

        $parentReference = $this->objectNodeLookup[spl_object_id($structure->getParent())];
        $dictionary->addReferenceEntry('Parent', $parentReference);

        $resources = $structure->getResources()->accept($this);
        $dictionary->addReferenceEntry('Resources', $resources);

        $dictionary->addNumberArrayEntry('MediaBox', $structure->getMediaBox());

        $contents = $structure->getContents()->accept($this, $structure);
        $dictionary->addReferenceArrayEntry('Contents', $contents);

        return $dictionary;
    }

    /**
     * @param Structure\Resources $structure
     *
     * @return BaseObject
     */
    public function visitResources(Structure\Resources $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();

        // see PDF320000_2008 14.2
        $procSet = ['PDF'];

        if (\count($structure->getFonts()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getFonts());
            $dictionary->addDictionaryEntry('Font', $fontDictionary);
            $procSet[] = 'Text';
        }

        if (\count($structure->getImages()) > 0) {
            $fontDictionary = $this->createReferenceDictionary($structure->getImages());
            $dictionary->addDictionaryEntry('XObject', $fontDictionary);
            $procSet[] = 'ImageC';
        }

        $dictionary->addTextArrayEntry('ProcSet', $procSet, '/');

        return $dictionary;
    }

    /**
     * @var ReferenceToken[]
     */
    private $referenceLookup;

    /**
     * @param IdentifiableStructureTrait[] $structures
     *
     * @return DictionaryToken
     */
    private function createReferenceDictionary(array $structures)
    {
        $dictionary = new DictionaryToken();
        foreach ($structures as $structure) {
            $identifier = $structure->getIdentifier();

            if (!\array_key_exists($identifier, $this->referenceLookup)) {
                $reference = $structure->accept($this);
                $this->referenceLookup[$identifier] = new ReferenceToken($reference);
            }

            $referenceToken = $this->referenceLookup[$identifier];
            $dictionary->setEntry($structure->getIdentifier(), $referenceToken);
        }

        return $dictionary;
    }

    /**
     * @param Structure\Contents $structure
     * @param Page $page
     *
     * @return BaseObject[]
     */
    public function visitContents(Structure\Contents $structure, Page $page): array
    {
        $contentVisitor = new ContentVisitor($page);

        /** @var BaseObject[] $baseObjects */
        $baseObjects = [];
        foreach ($structure->getContent() as $baseContent) {
            $baseObjects[] = $baseContent->accept($contentVisitor);
        }

        return $baseObjects;
    }

    /**
     * @param Structure\Font\Type1 $structure
     *
     * @return BaseObject
     */
    public function visitType1Font(Structure\Font\Type1 $structure): BaseObject
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/Font');
        $dictionary->addTextEntry('Subtype', '/Type1');
        $dictionary->addTextEntry('BaseFont', '/' . $structure->getBaseFont());

        return $dictionary;
    }

    /**
     * @param Structure\Image $structure
     *
     * @return BaseObject
     */
    public function visitImage(Structure\Image $structure): BaseObject
    {
        $stream = $this->file->addStreamObject($structure->getImageData());

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

    /**
     * @param Structure\Font\Structure\FontStream $structure
     *
     * @return StreamObject
     */
    public function visitFontStream(Structure\Font\Structure\FontStream $structure)
    {
        $stream = $this->file->addStreamObject($structure->getFontData());

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Subtype', '/' . $structure->getSubtype());

        return $stream;
    }

    /**
     * @param FontDescriptor $structure
     *
     * @return DictionaryObject
     */
    public function visitFontDescriptor(FontDescriptor $structure)
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/FontDescriptor');
        $dictionary->addTextEntry('FontName', $structure->getFontName());
        $dictionary->addTextEntry('Flags', $structure->getFlags());
        $dictionary->addTextEntry('FontBBox', $structure->getFontBBox());
        $dictionary->addTextEntry('ItalicAngle', $structure->getItalicAngle());
        $dictionary->addTextEntry('Ascent', $structure->getAscent());
        $dictionary->addTextEntry('Decent', $structure->getDecent());
        $dictionary->addTextEntry('CapHeight', $structure->getCapHeight());
        $dictionary->addTextEntry('StemV', $structure->getStemV());

        if ($structure->getFontFile3() !== null) {
            $reference = $structure->getFontFile3()->accept($this);
            $dictionary->addReferenceEntry('FontFile3', $reference);
        }

        return $dictionary;
    }

    /**
     * @param Structure\Font\Type0 $structure
     *
     * @return DictionaryObject
     */
    public function visitType0Font(Structure\Font\Type0 $structure)
    {
        $dictionary = $this->file->addDictionaryObject();

        $dictionary->addTextEntry('Type', '/Font');
        $dictionary->addTextEntry('Subtype', '/Type0');
        $dictionary->addTextEntry('BaseFont', '/' . $structure->getBaseFont());

        $encoding = $structure->getEncoding()->accept($this);
        $dictionary->addReferenceEntry('Encoding', $encoding);

        $descendantFont = $structure->getDescendantFont()->accept($this);
        $dictionary->addReferenceArrayEntry('DescendantFonts', [$descendantFont]);

        $toUnicode = $structure->getToUnicode()->accept($this);
        $dictionary->addReferenceEntry('ToUnicode', $toUnicode);

        return $dictionary;
    }

    /**
     * @param Structure\Font\Structure\CIDFont $structure
     *
     * @return DictionaryObject
     */
    public function visitCIDFont(Structure\Font\Structure\CIDFont $structure)
    {
        $dictionary = $this->file->addDictionaryObject();

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->addDictionaryEntry('CIDSystemInfo', $cidDictionary);

        $reference = $structure->getFontDescriptor()->accept($this);
        $dictionary->addReferenceEntry('FontDescriptor', $reference);

        $dictionary->addNumberEntry('DW', $structure->getDW());
        $dictionary->addNumberArrayEntry('W', $structure->getW());

        return $dictionary;
    }

    /**
     * @param CMap $structure
     *
     * @return StreamObject
     */
    public function visitCMap(CMap $structure)
    {
        $stream = $this->file->addStreamObject($structure->getCMapData());

        $dictionary = $stream->getMetaData();
        $dictionary->setTextEntry('Type', '/CMap');
        $dictionary->setTextEntry('CMapName', $structure->getCMapName());

        $cidDictionary = $structure->getCIDSystemInfo()->accept($this);
        $dictionary->setEntry('CIDSystemInfo', $cidDictionary);

        return $stream;
    }

    /**
     * @param CIDSystemInfo $structure
     *
     * @return DictionaryToken
     */
    public function visitCIDSystemInfo(CIDSystemInfo $structure)
    {
        $cidDictionary = new DictionaryToken();

        $cidDictionary->setTextEntry('Registry', $structure->getRegistry());
        $cidDictionary->setTextEntry('Ordering', $structure->getOrdering());
        $cidDictionary->setNumberEntry('Supplement', $structure->getSupplement());

        return $cidDictionary;
    }

    /**
     * @param Structure\Content $param
     *
     * @return StreamObject
     */
    public function visitContent(Structure\Content $param)
    {
        /*
        could compress at this point with
        if ($contentType === self::CONTENT_TYPE_TEXT && \extension_loaded('zlib')) {
            $this->dictionary->setTextEntry('Filter', '/FlatDecode');
            $this->content = gzcompress($this->content);
        }

        currently does not work and not the target of the project
        */
        return $this->file->addStreamObject($param->getContent());
    }
}
