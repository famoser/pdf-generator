<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\File;

use PdfGenerator\Backend\File\Token\ArrayToken;
use PdfGenerator\Backend\File\Token\Base\BaseToken;
use PdfGenerator\Backend\File\Token\DictionaryToken;
use PdfGenerator\Backend\File\Token\NumberToken;
use PdfGenerator\Backend\File\Token\ReferenceToken;
use PdfGenerator\Backend\File\Token\TextToken;

class TokenVisitor
{
    public function visitArrayToken(ArrayToken $token): string
    {
        $prefix = '';
        if ($token->getKey() !== null) {
            $prefix = $token->getKey()->accept($this) . ' ';
        }

        return $prefix . '[' . implode(' ', $this->evaluateTokenArray($token->getValues())) . ']';
    }

    public function visitNumberToken(NumberToken $token): string
    {
        return $token->getNumber();
    }

    public function visitDictionaryToken(DictionaryToken $token): string
    {
        $entries = [];
        $evaluatedTokens = $this->evaluateTokenArray($token->getKeyValue());
        foreach ($evaluatedTokens as $key => $value) {
            $entries[] = $this->transformToName($key) . ' ' . $value;
        }

        return '<<' . implode(' ', $entries) . '>>';
    }

    public function visitReferenceToken(ReferenceToken $token): string
    {
        return $token->getTarget()->getNumber() . ' 0 R';
    }

    public function visitTextToken(TextToken $token): string
    {
        $escapedText = strtr($token->getText(), ['\\' => '\\\\', ')' => '\\)', '(' => '\\(']);

        return '(' . $escapedText . ')';
    }

    public function visitNameToken(Token\NameToken $param)
    {
        // skipping escaping name because not decided by user
        return $this->transformToName($param->getName());
    }

    private function transformToName(string $value)
    {
        return '/' . $value;
    }

    /**
     * @param BaseToken[] $tokens
     *
     * @return string[]
     */
    private function evaluateTokenArray(array $tokens): array
    {
        $entries = [];
        foreach ($tokens as $key => $token) {
            $entries[$key] = $token->accept($this);
        }

        return $entries;
    }
}
