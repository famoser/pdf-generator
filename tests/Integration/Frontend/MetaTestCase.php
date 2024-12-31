<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Integration\Frontend;

use Famoser\PdfGenerator\Frontend\LinearDocument;
use Famoser\PdfGenerator\IR\Document\Meta;

class MetaTestCase extends LinearDocumentTestCase
{
    public function testMetaInFinalDocument(): void
    {
        // arrange
        $meta = Meta::createMeta(
            'en',
            'The RSA Cryptosystem',
            ['R.L. Rivest', 'A. Shamir', 'L. Adleman']
        );
        $meta->setDescription('A Method for Obtaining Digital Signatures and Public-Key Cryptosystems');
        $meta->setKeywords(['Cryptography', 'Public-Key']);
        $document = new LinearDocument(meta: $meta);

        // act
        $result = $this->render($document);

        // assert
        $this->assertStringContainsString('<rdf:li xml:lang="en">The RSA Cryptosystem</rdf:li>', $result);
    }
}
