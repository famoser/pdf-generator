<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Pdf\Tcpdf\Configuration;

class ConfigurationValidator extends \PdfGenerator\Document\Pdf\Configuration\ConfigurationValidator
{
    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    public function fontFamily(array $config, string $key)
    {
        $fontFamily = parent::fontFamily($config, $key);

        $fontFamilyValues = [TcpdfConfiguration::FONT_FAMILY_OPEN_SANS];
        if (!\in_array($fontFamily,   $fontFamilyValues, true)) {
            throw new \Exception($key . ' must be a avalid font family (one of ' . implode(',', $fontFamilyValues) . ')');
        }

        return $fontFamily;
    }
}
