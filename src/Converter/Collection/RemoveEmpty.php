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

namespace Ixnode\PhpWebCrawler\Converter\Collection;

use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Converter\Collection\Base\BaseConverterArray;

/**
 * Class RemoveEmpty
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-25)
 * @since 0.1.0 (2024-02-25) First version.
 */
class RemoveEmpty extends BaseConverterArray
{
    private const ZERO_RESULT = 0;

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(array|bool|float|int|string|null $value): array|bool|float|int|string|null
    {
        if (!is_array($value)) {
            return $value;
        }

        if (count($value) <= self::ZERO_RESULT) {
            return null;
        }

        $value = array_filter($value, fn(Json|bool|float|int|string|null $item) => !empty($item) || $item === '0');

        return array_values($value);
    }
}

