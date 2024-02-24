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
 * Class Sprintf
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Sprintf implements Converter
{
    private const WRAPPER_DEFAULT = '%s';

    /**
     * @param string $wrapper
     */
    public function __construct(
        private readonly string $wrapper = self::WRAPPER_DEFAULT
    )
    {
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(string|int $value): int|string
    {
        return sprintf($this->wrapper, $value);
    }
}

