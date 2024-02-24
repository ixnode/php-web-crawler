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

namespace Ixnode\PhpWebCrawler;

/**
 * Class to store and retrieve the version of WebCrawler
 *
 * @author  Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-23)
 * @since 0.1.0 (2024-02-23) First version.
 */
class Version
{
    /**
     * Current WebCrawler Version
     */
    final public const VERSION = '1.0.0';

    /**
     * Compares a WebCrawler version with the current one.
     *
     * @param string $version WebCrawler version to compare.
     *
     * @return bool|int Returns -1 if older, 0 if it is the same, 1 if version
     *                  passed as argument is newer.
     */
    public static function compare(string $version): bool|int
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version        = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}

