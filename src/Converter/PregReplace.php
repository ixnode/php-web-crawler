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
use LogicException;

/**
 * Class PregReplace
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class PregReplace extends BaseConverter
{
    /**
     * @param string $pattern
     * @param string $replacement
     */
    public function __construct(
        private readonly string $pattern,
        private readonly string $replacement = ''
    )
    {
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(bool|float|int|string|null $value): string
    {
        $replaced = preg_replace($this->pattern, $this->replacement, (string) $value);

        if (!is_string($replaced)) {
            throw new LogicException(sprintf('Unable to replace given value: %s', $value));
        }

        return $replaced;
    }
}

