<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Supporting;

use PdfGenerator\Frontend\Document;

class PrintBuffer
{
    /**
     * @var callable[]
     */
    private $printBuffer = [];

    /**
     * @var Document
     */
    private $pdfDocument;

    /**
     * @var float
     */
    private $width;

    /**
     * PrintBuffer constructor.
     *
     * @param Document $pdfDocument
     * @param float $width
     */
    public function __construct(Document $pdfDocument, float $width)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;
    }

    /**
     * @param callable $callable
     * @param callable $prepareArguments
     */
    public function prependPrintable(callable $callable, callable $prepareArguments = null)
    {
        $this->printBuffer = array_merge([$this->getPrintBufferEntry($callable, $prepareArguments)], $this->printBuffer);
    }

    /**
     * @param callable $callable
     * @param callable $prepareArguments
     */
    public function addPrintable(callable $callable, callable $prepareArguments = null)
    {
        $this->printBuffer[] = $this->getPrintBufferEntry($callable, $prepareArguments);
    }

    /**
     * @return \Closure
     */
    public function flushBufferClosure(): \Closure
    {
        return function () {
            foreach ($this->printBuffer as $item) {
                $item();
            }
        };
    }

    /**
     * @param self $buffer
     *
     * @return PrintBuffer
     */
    public static function createFromExisting(self $buffer)
    {
        $newBuffer = new self($buffer->pdfDocument, $buffer->width);
        $newBuffer->printBuffer = $buffer->printBuffer;

        return $newBuffer;
    }

    /**
     * @param callable $callable
     * @param callable $prepareArguments
     *
     * @return \Closure
     */
    private function getPrintBufferEntry(callable $callable, callable $prepareArguments = null)
    {
        $pdfDocument = $this->pdfDocument;

        $printConfig = $pdfDocument->getConfiguration();

        return function () use ($pdfDocument, $prepareArguments, $callable, $printConfig) {
            $pdfDocument->setConfiguration($printConfig);

            if ($prepareArguments !== null) {
                $arguments = $prepareArguments($pdfDocument);
            } else {
                $arguments = [$this->width];
            }

            $callable($pdfDocument, ...$arguments);
        };
    }
}
