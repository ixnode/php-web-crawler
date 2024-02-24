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

use LogicException;

/**
 * Class PregMatch
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
readonly class PregMatch implements Converter
{
    private const MATCH_DEFAULT = 0;

    /**
     * @param string $pattern
     * @param int $match
     */
    public function __construct(
        private string $pattern,
        private int $match = self::MATCH_DEFAULT
    )
    {
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(string|int|null $value): string|null
    {
        $matches = [];
        if (!preg_match($this->pattern, (string) $value, $matches)) {
            return null;
        }

        if (!array_key_exists($this->match, $matches)) {
            return null;
        }

        $text = $matches[$this->match];

        if (!is_string($text)) {
            return null;
        }

        return $text;
    }
}

