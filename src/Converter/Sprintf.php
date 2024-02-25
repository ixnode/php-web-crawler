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

use Ixnode\PhpWebCrawler\Converter\Base\BaseConverter;

/**
 * Class Sprintf
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Sprintf extends BaseConverter
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
    public function getValue(string|int|null $value): string
    {
        return sprintf($this->wrapper, $value);
    }
}

