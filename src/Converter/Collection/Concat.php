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

use Ixnode\PhpWebCrawler\Converter\Collection\Base\BaseConcat;
use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverterScalar;

/**
 * Class Concat
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-26)
 * @since 0.1.0 (2024-02-26) First version.
 */
class Concat extends BaseConcat
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
        int|null $chunkSize = self::NUMBER_CHUNK_SIZE,
        string $separator = self::SEPARATOR,
        array $scalarConverters = []
    )
    {
        parent::__construct($chunkSize, $separator, $scalarConverters);
    }

    /**
     * Chunk and join the given array.
     *
     * @inheritdoc
     */
    protected function chunkAndJoinArray(array $array, int|null $chunkSize, string $separator): array|string
    {
        foreach ($array as &$value) {
            if (is_null($value)) {
                $value = '<null>';
            }
        }

        if (is_null($chunkSize) || $chunkSize <= self::ZERO_RESULT) {
            return implode($separator, $array);
        }

        $chunks = array_chunk($array, $chunkSize);

        foreach ($chunks as &$chunk) {
            foreach ($chunk as $key => &$value) {
                if (!array_key_exists($key, $this->scalarConverters) || is_null($this->scalarConverters[$key])) {
                    continue;
                }

                $value = $this->scalarConverters[$key]->getValue($value);
            }
        }

        return array_map(fn($chunk) => join($separator, $chunk), $chunks);
    }
}

