<?php

/*
 * This file is part of the ixnode/php-web-crawler project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ixnode\PhpWebCrawler\Converter;

use LogicException;

/**
 * Class DateParser
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class DateParser implements Converter
{
    private const WRAPPER_DEFAULT = '%s';

    /**
     * @param string $format
     * @param string $wrapper
     */
    public function __construct(
        private readonly string $format,
        private readonly string $wrapper = self::WRAPPER_DEFAULT)
    {
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(string|int|null $value): string|int
    {
        $value = sprintf($this->wrapper, $value);

        $date = date_create_from_format($this->format, (string) $value);

        if ($date === false) {
            throw new LogicException(sprintf('Unable to parse the date: %s', $value));
        }

        return date_timestamp_get($date);
    }
}

