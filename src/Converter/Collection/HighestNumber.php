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
 * Class HighestNumber
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-27)
 * @since 0.1.0 (2024-02-27) First version.
 */
class HighestNumber extends BaseConverterArray
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

        $hasJson = false;

        foreach ($value as $item) {
            if ($item instanceof Json) {
                $hasJson = true;
                break;
            }
        }

        if ($hasJson) {
            return $value;
        }

        /** @var array<int, bool|float|int|string|null> $value */
        return max(array_map(fn($item) => intval($item), $value));
    }
}

