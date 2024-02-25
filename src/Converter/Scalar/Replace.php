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

use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverter;
use LogicException;

/**
 * Class Replace
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-26)
 * @since 0.1.0 (2024-02-26) First version.
 */
class Replace extends BaseConverter
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
    public function getValue(bool|float|int|string|null $value): bool|float|int|string|null
    {
        if (is_null($value)) {
            return null;
        }

        if (is_null($this->search)) {
            return $value;
        }

        $value = str_replace($this->search, $this->replace, (string) $value);

        if (!is_string($value)) {
            throw new LogicException('Unable to replace given value.');
        }

        return $value;
    }
}

