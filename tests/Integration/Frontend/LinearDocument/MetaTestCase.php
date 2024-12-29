<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Frontend\LinearDocument;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Style\BlockStyle;
use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Frontend\Resource\Image;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\IR\Document\Meta;
use Famoser\PdfGenerator\Tests\Resources\ResourcesProvider;

class MetaTestCase extends LinearDocumentTestCase
{
    public function testMetaInFinalDocument(): void
    {
        // arrange
        $meta = Meta::createMeta(
            'en',
            'The RSA Cryptosystem',
            'A Method for Obtaining Digital Signatures and Public-Key Cryptosystems',
            ['R.L. Rivest', 'A. Shamir', 'L. Adleman']
        );
        $meta->setKeywordSubjects(['Cryptography', 'Public-Key']);
        $document = new LinearDocument(meta: $meta);

        // act
        $result = $this->render($document);

        // assert
        $this->assertStringContainsString('<rdf:li xml:lang="en">The RSA Cryptosystem</rdf:li>', $result);
    }
}
