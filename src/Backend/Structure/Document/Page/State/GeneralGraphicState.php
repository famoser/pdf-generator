<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

class GeneralGraphicState extends BaseState
{
    const LINE_CAP_BUTT = 0;
    const LINE_CAP_ROUND = 1;
    const LINE_CAP_PROJECTING_SQUARE = 2;

    const LINE_JOIN_MITER = 0;
    const LINE_JOIN_ROUND = 1;
    const LINE_JOIN_BEVEL = 2;

    /**
     * transformation matrix
     * translate: [1, 0, 0, 1, x, y]
     * scale: [scale-x, 0, 0, scale-y, 0, 0]
     * translate & scale: [scale-x, 0, 0, scale-y, x, y]
     * rotation: [cos q, sin q, -sin q, cos q, 0, 0]
     * skew: [1, tan a, tan b, 1, 0, 0]
     * pdf-operator: cm.
     *
     * @var float[]
     */
    private $currentTransformationMatrix = [1, 0, 0, 1, 0, 0];

    /**
     * line width
     * if the value is 0, the thinnest line possible on the device will be rendered
     * pdf-operator: w.
     *
     * @var float
     */
    private $lineWidth = 0;

    /**
     * how the end of a line looks like
     * butt cap stops squared at the end of the path
     * round cap produces a semicircular arch with the diameter = @see lineWidth
     * projecting square cap stops squared at the end of the path + @see lineWidth/2
     * pdf-operator: J.
     *
     * @var int
     */
    private $lineCap = self::LINE_CAP_BUTT;

    /**
     * how two meeting lines are brought together
     * miter join produces a sharp edge, by extending the meeting lines until the @see miterLimit
     * round join creates an arch around the edge with diameter = @see lineWidth
     * bevel join produces a flat edge, by adding a triangle into the free space produced by the two lines with butt caps meeting
     * pdf-operator: j.
     *
     * @var int
     */
    private $lineJoin = self::LINE_JOIN_MITER;

    /**
     * impose maximum height of the sharp edge produced by a miter join
     * when the threshold is reached, a bevel join is used
     * pdf-operator: M.
     *
     * @var float
     */
    private $miterLimit = 2.0;

    /**
     * the pattern of on / off parts, repeated indefinitely
     * if empty, then a solid line will be rendered
     * pdf-operator: d together with @see $dashPhase.
     *
     * @var float[]
     */
    private $dashArray = [];

    /**
     * the pattern shift at start
     * pdf-operator: d together with @see $dashArray.
     *
     * @var float
     */
    private $dashPhase = 0;

    public function setMinimalAngleOfMiterJoin(float $angle)
    {
        $this->miterLimit = $this->lineWidth * sin($angle / 2);
    }

    /**
     * @return float[]
     */
    public function getCurrentTransformationMatrix(): array
    {
        return $this->currentTransformationMatrix;
    }

    /**
     * @param float[] $currentTransformationMatrix
     */
    public function setCurrentTransformationMatrix(array $currentTransformationMatrix): void
    {
        \assert(\count($currentTransformationMatrix) === 6);

        $this->currentTransformationMatrix = $currentTransformationMatrix;
    }

    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    public function setLineWidth(float $lineWidth): void
    {
        $this->lineWidth = $lineWidth;
    }

    public function getLineCap(): int
    {
        return $this->lineCap;
    }

    public function setLineCap(int $lineCap): void
    {
        \assert($lineCap >= self::LINE_CAP_BUTT && $lineCap <= self::LINE_CAP_PROJECTING_SQUARE);

        $this->lineCap = $lineCap;
    }

    public function getLineJoin(): int
    {
        return $this->lineJoin;
    }

    public function setLineJoin(int $lineJoin): void
    {
        \assert($lineJoin >= self::LINE_JOIN_MITER && $lineJoin <= self::LINE_JOIN_BEVEL);

        $this->lineJoin = $lineJoin;
    }

    public function getMiterLimit(): float
    {
        return $this->miterLimit;
    }

    public function setMiterLimit(float $miterLimit): void
    {
        $this->miterLimit = $miterLimit;
    }

    /**
     * @return float[]
     */
    public function getDashArray(): array
    {
        return $this->dashArray;
    }

    /**
     * @param float[] $dashArray
     */
    public function setDashArray(array $dashArray): void
    {
        $this->dashArray = $dashArray;
    }

    public function getDashPhase(): float
    {
        return $this->dashPhase;
    }

    public function setDashPhase(float $dashPhase): void
    {
        $this->dashPhase = $dashPhase;
    }

    /**
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitGeneralGraphicState($this);
    }
}
