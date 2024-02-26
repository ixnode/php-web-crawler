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

namespace Ixnode\PhpWebCrawler\Converter\Scalar;

use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverterScalar;

/**
 * Class Number
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Number extends BaseConverterScalar
{
    /**
     * @param string|string[]|null $search
     * @param string|string[] $replace
     */
    public function __construct(
        private readonly string|array|null $search = null,
        private readonly string|array $replace = '',
    )
    {
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(bool|float|int|string|null $value): float|int|null
    {
        return match (true) {
            is_null($value) => null,
            is_int($value),
            is_float($value) => $value,
            is_bool($value) => $value ? 1 : 0,
            default => (int) (!is_null($this->search) ? str_replace($this->search, $this->replace, $value) : $value),
        };
    }
}

