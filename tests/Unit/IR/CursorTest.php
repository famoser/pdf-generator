<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Unit\IR;

use PdfGenerator\IR\Cursor;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    public function testIsBiggerThan_onlyPageSet_correctResult()
    {
        $bigCursor = new Cursor(0, 0, 0);
        $smallCursor = new Cursor(0, 0, 1);

        $this->ensureBigger($bigCursor, $smallCursor);
    }

    public function testIsBiggerThan_pageAndYSet_correctResult()
    {
        $bigCursor = new Cursor(0, 200, 0);
        $smallCursor = new Cursor(0, 0, 1);

        $this->ensureBigger($bigCursor, $smallCursor);
    }

    public function testIsBiggerThan_xSet_xIsIgnored()
    {
        $bigCursor = new Cursor(200, 0, 0);
        $smallCursor = new Cursor(0, 0, 1);

        $this->ensureBigger($bigCursor, $smallCursor);
    }

    public function testIsBiggerThan_equalPage_correctResult()
    {
        $bigCursor = new Cursor(0, 100, 0);
        $smallCursor = new Cursor(0, 120, 0);

        $this->ensureBigger($bigCursor, $smallCursor);
    }

    public function testIsBiggerThan_equal_correctResult()
    {
        $bigCursor = new Cursor(0, 0, 0);
        $smallCursor = new Cursor(0, 0, 0);

        $this->assertFalse($bigCursor->isBiggerThan($bigCursor));
        $this->assertFalse($smallCursor->isBiggerThan($smallCursor));
        $this->assertFalse($bigCursor->isBiggerThan($smallCursor));
        $this->assertFalse($smallCursor->isBiggerThan($bigCursor));
    }

    private function ensureBigger(Cursor $bigCursor, Cursor $smallCursor)
    {
        $this->assertTrue($smallCursor->isBiggerThan($bigCursor));
        $this->assertFalse($bigCursor->isBiggerThan($smallCursor));
    }
}
