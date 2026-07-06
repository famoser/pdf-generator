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

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Page\PageProperties;
use Famoser\PdfGenerator\Frontend\Page\Rotation;
use Famoser\PdfGenerator\Frontend\Page\Transition\Transition;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\Tests\Integration\Frontend\TestUtils\Render;
use PHPUnit\Framework\TestCase;

class PropertiesTest extends TestCase
{
    use Render;

    public function testRotationRespected(): void
    {
        // arrange
        $document = new Document();
        $normalText = new TextStyle(Font::createFromDefault());
        $text = new Text();
        $text->addSpan('Hi mom', $normalText, 12);

        // act
        $properties = new PageProperties(rotation: Rotation::RotatedClockwise);
        $document->addPage($properties);
        $document->add($text);

        $properties = new PageProperties(rotation: Rotation::RotatedCounterclockwise);
        $document->addPage($properties);
        $document->add($text);
        $result = $this->render($document);

        // assert
        $this->assertStringContainsString('Hi mom', $result);
    }

    public function testTransition(): void
    {
        // arrange
        $document = new Document();
        $normalText = new TextStyle(Font::createFromDefault());
        $text = new Text();
        $text->addSpan('Hi mom', $normalText, 12);

        // act
        $properties = new PageProperties();
        $properties->setTransition(Transition::Fade);
        $document->addPage($properties);
        $document->add($text);

        $properties = new PageProperties();
        $document->addPage($properties);
        $text->addSpan(', how are you?', $normalText, 12);
        $document->add($text);
        $result = $this->render($document);

        // assert
        $this->assertStringContainsString('Hi mom', $result);
    }
}
