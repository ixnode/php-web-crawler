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

namespace Ixnode\PhpWebCrawler\Output;

use DOMNode;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Output\Base\BaseOutput;

/**
 * Class Field
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Field extends BaseOutput
{
    /**
     * Parses the given xpath.
     *
     * @inheritdoc
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|null
    {
        if (count($this->values) === 0) {
            return $this->getStructuredData(null);
        }

        if (count($this->values) === 1) {
            return $this->getStructuredData($this->values[0]->parse($xpath, $node));
        }

        $data = [];

        foreach ($this->values as $value) {
            $parsed = $value->parse($xpath, $node);

            $data[] = match (true) {
                $parsed instanceof Json => $parsed->getArray(),
                default => $parsed,
            };
        }

        return $this->getStructuredData(new Json($data));
    }
}
