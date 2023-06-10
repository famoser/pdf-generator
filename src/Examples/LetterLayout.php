<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Examples;

class LetterLayout
{
    private readonly \PdfGenerator\IR\Document\Content\Text\TextStyle $headerStyle;

    private readonly \PdfGenerator\IR\Document\Content\Text\TextStyle $bodyStyle;

    public function withHeaderStyle(\PdfGenerator\IR\Document\Content\Text\TextStyle $textStyle)
    {
    }

    public function withBodyStyle(\PdfGenerator\IR\Document\Content\Text\TextStyle $textStyle)
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

    public function withBodyText(string $text)
    {
    }
}
