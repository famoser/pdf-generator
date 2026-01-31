<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Utils;

/**
 * Some algorithms in here are inspired by https://github.com/jorgecasas/php-ml
 * However, they have been transformed for the 3x3 case, and simplified accordingly.
 */
class TransformationMatrixCalculator
{
    /**
     * @param (float|int)[] $sourceState
     * @param (float|int)[] $targetState
     *
     * @return float[]
     */
    public static function getTransformationMatrix(array $sourceState, array $targetState): array
    {
        $sourceMatrix = self::convertToMatrix($sourceState);
        $targetMatrix = self::convertToMatrix($targetState);

        /**
         * $sourceState * $result = $targetState
         * $result = $targetState * $sourceState^-1
         * OK to assume $sourceState is invertible, as a non-invertible matrix is undefined behavior by the PDF spec.
         */
        $sourceMatrixInverted = self::invertMatrix($sourceMatrix);
        $resultMatrix = self::multiplyMatrix($targetMatrix, $sourceMatrixInverted);

        return self::convertFromMatrixRounded($resultMatrix);
    }

    /**
     * @param float[][] $matrix
     *
     * @return float[][]
     */
    private static function invertMatrix(array $matrix): array
    {
        [$LU, $piv] = self::LUDecompose($matrix);
        $identity = self::identityMatrix();

        return self::LUDecomposeSolve($identity, $LU, $piv);
    }

    /**
     * @param float[][] $matrix
     *
     * @return array{float[][], int[]}
     */
    private static function LUDecompose(array $matrix): array
    {
        $piv = [0, 1, 2];
        $LU = $matrix;

        $LUcolj = [];

        // Outer loop.
        for ($j = 0; $j < 3; ++$j) {
            // Make a copy of the j-th column to localize references.
            $LUcolj[0] = &$LU[0][$j];
            $LUcolj[1] = &$LU[1][$j];
            $LUcolj[2] = &$LU[2][$j];

            // Apply previous transformations.
            for ($i = 0; $i < 3; ++$i) {
                $LUrowi = $LU[$i];
                // Most of the time is spent in the following dot product.
                $kmax = min($i, $j);
                $s = 0.0;
                for ($k = 0; $k < $kmax; ++$k) {
                    $s += $LUrowi[$k] * $LUcolj[$k];
                }

                $LUrowi[$j] = $LUcolj[$i] -= $s;
            }

            // Find pivot and exchange if necessary.
            $p = $j;
            for ($i = $j + 1; $i < 3; ++$i) {
                if (abs($LUcolj[$i]) > abs($LUcolj[$p])) {
                    $p = $i;
                }
            }

            if ($p != $j) {
                $t = $LU[$p][0];
                $LU[$p][0] = $LU[$j][0];
                $LU[$j][0] = $t;

                $t = $LU[$p][1];
                $LU[$p][1] = $LU[$j][1];
                $LU[$j][1] = $t;

                $t = $LU[$p][2];
                $LU[$p][2] = $LU[$j][2];
                $LU[$j][2] = $t;

                $k = $piv[$p];
                $piv[$p] = $piv[$j];
                $piv[$j] = $k;
            }

            // Compute multipliers.
            if (0.0 != $LU[$j][$j]) {
                for ($i = $j + 1; $i < 3; ++$i) {
                    $LU[$i][$j] /= $LU[$j][$j];
                }
            }
        }

        return [$LU, $piv];
    }

    /**
     * @param float[][] $matrix
     * @param float[][] $LU
     * @param int[]   $piv
     *
     * @return float[][]
     */
    private static function LUDecomposeSolve(array $matrix, array $LU, array $piv): array
    {
        if (0 == $LU[0][0] || 0 == $LU[1][1] || 0 == $LU[2][2]) {
            // matrix cannot be inverted; use fallback
            // if the matrix is not invertible, PDF behavior is undefined
            // hence returning here the identity does not really matter
            return self::identityMatrix();
        }

        // Copy right hand side with pivoting
        $X = self::getSubMatrix($matrix, $piv, 0, 2);
        // Solve L*Y = B(piv,:)
        for ($k = 0; $k < 3; ++$k) {
            for ($i = $k + 1; $i < 3; ++$i) {
                for ($j = 0; $j < 3; ++$j) {
                    $X[$i][$j] -= $X[$k][$j] * $LU[$i][$k];
                }
            }
        }

        // Solve U*X = Y;
        for ($k = 2; $k >= 0; --$k) {
            for ($j = 0; $j < 3; ++$j) {
                $X[$k][$j] /= $LU[$k][$k];
            }

            for ($i = 0; $i < $k; ++$i) {
                for ($j = 0; $j < 3; ++$j) {
                    $X[$i][$j] -= $X[$k][$j] * $LU[$i][$k];
                }
            }
        }

        return $X;
    }

    /**
     * @param float[][] $matrix
     * @param int[]   $RL
     *
     * @return float[][]
     */
    private static function getSubMatrix(array $matrix, array $RL, int $j0, int $jF): array
    {
        $m = count($RL);
        $n = $jF - $j0;
        $R = array_fill(0, $m, array_fill(0, $n + 1, 0.0));

        for ($i = 0; $i < $m; ++$i) {
            for ($j = $j0; $j <= $jF; ++$j) {
                $R[$i][$j - $j0] = $matrix[$RL[$i]][$j];
            }
        }

        return $R;
    }

    /**
     * @param float[][] $matrix1
     * @param float[][] $matrix2
     *
     * @return float[][]
     */
    private static function multiplyMatrix(array $matrix1, array $matrix2): array
    {
        $product = [];
        foreach ($matrix1 as $row => $rowData) {
            for ($col = 0; $col < 3; ++$col) {
                $columnData = array_column($matrix2, $col);
                $sum = 0;
                foreach ($rowData as $key => $valueData) {
                    $sum += $valueData * $columnData[$key];
                }

                $product[$row][$col] = $sum;
            }
        }

        return $product;
    }

    /**
     * @param (float|int)[] $tm
     *
     * @return float[][]
     */
    private static function convertToMatrix(array $tm): array
    {
        return [
            [(float) $tm[0], (float) $tm[1], 0.0],
            [(float) $tm[2], (float) $tm[3], 0.0],
            [(float) $tm[4], (float) $tm[5], 1.0],
        ];
    }

    /**
     * @param float[][] $m
     *
     * @return float[]
     */
    private static function convertFromMatrixRounded(array $m): array
    {
        return [
            round($m[0][0], 6),
            round($m[0][1], 6),
            round($m[1][0], 6),
            round($m[1][1], 6),
            round($m[2][0], 6),
            round($m[2][1], 6),
        ];
    }

    /**
     * @return float[][]
     */
    private static function identityMatrix(): array
    {
        return [
            [1.0, 0.0, 0.0],
            [0.0, 1.0, 0.0],
            [0.0, 0.0, 1.0],
        ];
    }
}
