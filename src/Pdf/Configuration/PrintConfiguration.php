<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf\Configuration;

class PrintConfiguration extends DrawConfiguration
{
    const FONT_FAMILY = 'FONT_FAMILY';
    const FONT_SIZE = 'FONT_SIZE';

    const FONT_WEIGHT = 'FONT_WEIGHT';
    const FONT_WEIGHT_NORMAL = 'FONT_WEIGHT_NORMAL';
    const FONT_WEIGHT_BOLD = 'FONT_WEIGHT_BOLD';

    const TEXT_COLOR = 'TEXT_COLOR';

    const TEXT_ALIGN = 'TEXT_ALIGN';
    const TEXT_ALIGN_LEFT = 'TEXT_ALIGN_LEFT';
    const TEXT_ALIGN_RIGHT = 'TEXT_ALIGN_RIGHT';

    /**
     * @var string|null
     */
    private $fontFamily = null;

    /**
     * @var float|null
     */
    private $fontSize = null;

    /**
     * @var string
     */
    private $fontWeight = self::FONT_WEIGHT_NORMAL;

    /**
     * @var string|null
     */
    private $textColor = null;

    /**
     * @var string
     */
    private $textAlign = self::TEXT_ALIGN_LEFT;

    /**
     * @var ConfigurationValidator
     */
    private $configurationValidator;

    /**
     * PrintConfiguration constructor.
     */
    public function __construct()
    {
        $validator = new ConfigurationValidator();
        parent::__construct($validator);

        $this->configurationValidator = $validator;
    }

    /**
     * @param PrintConfiguration $existing
     *
     * @throws \Exception
     *
     * @return PrintConfiguration
     */
    public static function createFromExisting(self $existing)
    {
        $new = new self();
        $new->setConfiguration($existing->getConfiguration());

        return $new;
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function setConfiguration(array $config)
    {
        parent::setConfiguration($config);

        if (isset($config[self::FONT_FAMILY])) {
            $this->fontFamily = $this->configurationValidator->fontFamily($config, self::FONT_FAMILY);
        }

        if (isset($config[self::FONT_SIZE])) {
            $this->fontSize = $this->configurationValidator->fontSize($config, self::FONT_SIZE);
        }

        if (isset($config[self::FONT_WEIGHT])) {
            $this->fontWeight = $this->configurationValidator->fontWeight($config, self::FONT_WEIGHT);
        }

        if (isset($config[self::TEXT_COLOR])) {
            $this->textColor = $this->configurationValidator->color($config, self::TEXT_COLOR);
        }

        if (isset($config[self::TEXT_ALIGN])) {
            $this->textAlign = $this->configurationValidator->textAlign($config, self::TEXT_ALIGN);
        }
    }

    /**
     * @return string|null
     */
    public function getFontFamily(): ?string
    {
        return $this->fontFamily;
    }

    /**
     * @return float|null
     */
    public function getFontSize(): ?float
    {
        return $this->fontSize;
    }

    /**
     * @return string
     */
    public function getFontWeight(): string
    {
        return $this->fontWeight;
    }

    /**
     * @return string|null
     */
    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    /**
     * @return string
     */
    public function getTextAlign(): string
    {
        return $this->textAlign;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return parent::getConfiguration() + [
                self::FONT_FAMILY => $this->getFontFamily(),
                self::FONT_SIZE => $this->getFontSize(),
                self::FONT_WEIGHT => $this->getFontWeight(),
                self::TEXT_COLOR => $this->getTextColor(),
                self::TEXT_ALIGN => $this->getTextAlign(),
            ];
    }
}
