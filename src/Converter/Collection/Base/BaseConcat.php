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

namespace Ixnode\PhpWebCrawler\Converter\Collection\Base;

use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverterScalar;

/**
 * Abstract class BaseConcat
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-26)
 * @since 0.1.0 (2024-02-26) First version.
 */
abstract class BaseConcat extends BaseConverterArray
{
    protected const NUMBER_CHUNK_SIZE = 1;

    protected const SEPARATOR = ', ';

    protected const ZERO_RESULT = 0;

    /**
     * @param int|null $chunkSize
     * @param string $separator
     * @param array<int, BaseConverterScalar|null> $scalarConverters
     */
    public function __construct(
        protected int|null $chunkSize = self::NUMBER_CHUNK_SIZE,
        protected string $separator = self::SEPARATOR,
        protected array $scalarConverters = []
    )
    {
    }

    /**
     * Chunk and join the given array.
     *
     * @param array<int, bool|float|int|string|null> $array
     * @param int|null $chunkSize
     * @param string $separator
     * @return array<int, string>|string
     */
    abstract protected function chunkAndJoinArray(array $array, int|null $chunkSize, string $separator): array|string;

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
        return $this->chunkAndJoinArray($value, $this->chunkSize, $this->separator);
    }
}

