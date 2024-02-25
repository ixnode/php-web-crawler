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

use Ixnode\PhpWebCrawler\Source\Base\BaseSource;
use Ixnode\PhpWebCrawler\Tests\Unit\FileCrawlerTest;
use LogicException;

/**
 * Class File
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @link FileCrawlerTest
 */
class File extends BaseSource
{
    /**
     * Adds the source to this object.
     *
     * @inheritdoc
     */
    public function addSource(string $source): void
    {
        $contents = file_get_contents($source);

        if ($contents === false) {
            throw new LogicException(sprintf('Unable to crawl the file: %s', $source));
        };

        $this->source = $contents;
    }
}
