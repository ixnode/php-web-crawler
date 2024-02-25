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

namespace Ixnode\PhpWebCrawler\Value;

use DOMNode;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Source\Url;
use Ixnode\PhpWebCrawler\Value\Base\BaseValue;

/**
 * Class LastUrl
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-25)
 * @since 0.1.0 (2024-02-25) First version.
 */
class LastUrl extends BaseValue
{
    /**
     * Parses the given xpath.
     *
     * @inheritdoc
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|float|bool|null
    {
        if (!$this->initiator instanceof Url) {
            return null;
        }

        return $this->applyChildren($this->initiator->getLastUrl());
    }
}
