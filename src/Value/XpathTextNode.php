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
use DOMNodeList;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Value\Base\BaseValue;
use LogicException;

/**
 * Class XpathTextNode
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathTextNode extends BaseValue
{
    /**
     * Parse the given xpath.
     *
     * @inheritdoc
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|float|bool|null
    {
        $domNodeList = $xpath->query((string) $this->value, $node);

        if (!$domNodeList instanceof DOMNodeList) {
            throw new LogicException('Unexpected result from xpath query');
        }

        if ($domNodeList->length === 0) {
            return $this->applyChildren(null);
        }

        if ($domNodeList->length === 1) {
            $item = $domNodeList->item(0);

            if (!$item instanceof DOMNode) {
                throw new LogicException('Unexpected result from xpath query');
            }

            return $this->applyChildren($item->textContent);
        }

        $data = [];

        foreach ($domNodeList as $domNode) {
            $data[] = $this->applyChildren($domNode->textContent);
        }

        return $this->applyChildrenArray($data);
    }
}
