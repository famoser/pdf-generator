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

use PdfGenerator\IR\Structure\Document;

class InvoiceLayout
{
    private Document $document;

    private Document\Page\Content\Text\TextStyle $headerStyle;

    private Document\Page\Content\Text\TextStyle $bodyStyle;

    /**
     * InvoiceLayout constructor.
     */
    public function __construct(Document $document, string $headerFontPath, string $bodyFontPath)
    {
        $this->document = $document;

        $headerFont = $this->document->getOrCreateEmbeddedFont($headerFontPath);
        $this->headerStyle = new Document\Page\Content\Text\TextStyle($headerFont, 8);

        $bodyFont = $this->document->getOrCreateEmbeddedFont($bodyFontPath);
        $this->bodyStyle = new Document\Page\Content\Text\TextStyle($bodyFont, 6);
    }

    public function withHeaderStyle(Document\Page\Content\Text\TextStyle $textStyle)
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
}
