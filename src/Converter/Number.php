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

/**
 * Class Number
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Number implements Converter
{
    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(string|int|null $value): string|int|null
    {
        if (is_null($value)) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        return (int) str_replace(',', '', $value);
    }
}

