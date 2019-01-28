<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Pdf;

use PdfGenerator\Pdf\Cursor;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    public function testIsLowerOnPage_onlyPageSet_correctResult()
    {
        $highCursor = new Cursor(0, 0, 0);
        $lowCursor = new Cursor(0, 0, 1);

        $this->ensureHighLow($highCursor, $lowCursor);
    }

    public function testIsLowerOnPage_pageAndYSet_correctResult()
    {
        $highCursor = new Cursor(0, 200, 0);
        $lowCursor = new Cursor(0, 0, 1);

        $this->ensureHighLow($highCursor, $lowCursor);
    }

    public function testIsLowerOnPage_xSet_xIsIgnored()
    {
        $highCursor = new Cursor(200, 0, 0);
        $lowCursor = new Cursor(0, 0, 1);

        $this->ensureHighLow($highCursor, $lowCursor);
    }

    public function testIsLowerOnPage_equalPage_correctResult()
    {
        $highCursor = new Cursor(0, 100, 0);
        $lowCursor = new Cursor(0, 120, 0);

        $this->ensureHighLow($highCursor, $lowCursor);
    }

    public function testIsLowerOnPage_equal_correctResult()
    {
        $highCursor = new Cursor(0, 0, 0);
        $lowCursor = new Cursor(0, 0, 0);

        $this->assertFalse($highCursor->isLowerOnPageThan($highCursor));
        $this->assertFalse($lowCursor->isLowerOnPageThan($lowCursor));
        $this->assertFalse($highCursor->isLowerOnPageThan($lowCursor));
        $this->assertFalse($lowCursor->isLowerOnPageThan($highCursor));
    }

    private function ensureHighLow(Cursor $highCursor, Cursor $lowCursor)
    {
        $this->assertTrue($lowCursor->isLowerOnPageThan($highCursor));
        $this->assertFalse($highCursor->isLowerOnPageThan($lowCursor));
    }
}
