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

class ConfigurationValidator
{
    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    protected static function readNullableHex(array $config, string $key)
    {
        $value = $config[$key];
        if ($value !== null && !(\is_string($value) && preg_match('/^#([a-f0-9]){6}$/', $value))) {
            throw new \Exception($key . ' config must be a hex value like #000000');
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return float|int
     */
    protected static function readNullableNumber(array $config, string $key)
    {
        $value = $config[$key];
        if ($value !== null && !\is_float($value) && !\is_int($value)) {
            throw new \Exception($key . ' config must be a number (either floats or integers) or null');
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    protected static function readNullableText(array $config, string $key)
    {
        $value = $config[$key];
        if ($value !== null && !\is_string($value)) {
            throw new \Exception($key . ' config must be a string value or null');
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return float|int
     */
    protected static function readNumber(array $config, string $key)
    {
        $value = $config[$key];
        if (!\is_float($value) && !\is_int($value)) {
            throw new \Exception($key . ' config must be a number (either floats or integers)');
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    protected static function readText(array $config, string $key)
    {
        $value = $config[$key];
        if (!\is_string($value)) {
            throw new \Exception($key . ' config must be a string value');
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     * @param array $possibleValues
     *
     * @throws \Exception
     *
     * @return string
     */
    protected static function readConstValue(array $config, string $key, array $possibleValues)
    {
        $value = self::readText($config, $key);

        if (!\in_array($value, $possibleValues, true)) {
            throw new \Exception($key . ' config must one of ' . implode(',', $possibleValues));
        }

        return $value;
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string|null
     */
    public function color(array $config, string $key)
    {
        return self::readNullableHex($config, $key);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string|null
     */
    public function fontFamily(array $config, string $key)
    {
        return self::readNullableText($config, $key);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return float|int|null
     */
    public function fontSize(array $config, string $key)
    {
        return self::readNullableNumber($config, $key);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    public function textAlign(array $config, string $key)
    {
        return self::readConstValue($config, $key, [PrintConfiguration::TEXT_ALIGN_LEFT, PrintConfiguration::TEXT_ALIGN_RIGHT]);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    public function fontWeight(array $config, string $key)
    {
        return self::readConstValue($config, $key, [PrintConfiguration::FONT_WEIGHT_NORMAL, PrintConfiguration::FONT_WEIGHT_BOLD]);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @throws \Exception
     *
     * @return string
     */
    public function size(array $config, string $key)
    {
        return self::readNumber($config, $key);
    }
}
