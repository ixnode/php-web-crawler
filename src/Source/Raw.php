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

/**
 * Class Raw
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @link FileCrawlerTest
 */
class Raw extends BaseSource
{
    /**
     * Adds the source to this object.
     *
     * @inheritdoc
     */
    public function addSource(string $source): void
    {
        $this->source = $source;
    }
}
