<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout;

use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Text\TextWriter;

class LetterLayout
{
    /**
     * @var Document\Page\Content\Text\TextStyle
     */
    private $headerStyle;

    /**
     * @var Document\Page\Content\Text\TextStyle
     */
    private $bodyStyle;

    public function withHeaderStyle(Document\Page\Content\Text\TextStyle $textStyle)
    {
    }

    public function withBodyStyle(Document\Page\Content\Text\TextStyle $textStyle)
    {
    }

    public function withLogo(string $logoPath)
    {
    }

    public function withSender(array $senderLines)
    {
    }

    public function withReceiver(array $receiverLines)
    {
    }

    public function withDate(string $date)
    {
    }

    public function withSubject(string $subject)
    {
    }

    public function withBody(TextWriter $textWriter)
    {
    }

    public function withBodyText(string $text)
    {
    }
}
