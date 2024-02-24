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

namespace Ixnode\PhpWebCrawler\Source;

use LogicException;

/**
 * Class Url
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Url extends Source
{
    private const CONNECT_TIMEOUT = 5;

    private const RETURN_TRANSFER = 1;

    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0';

    /**
     * Adds the source to this object.
     *
     * @inheritdoc
     */
    public function addSource(string $source): void
    {
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $source);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, self::RETURN_TRANSFER);
        curl_setopt($curlInit, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($curlInit, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curlInit);
        curl_close($curlInit);

        if (!is_string($response)) {
            throw new LogicException(sprintf('Unable to crawl the url: %s', $source));
        }

        $this->source = $response;
    }
}
