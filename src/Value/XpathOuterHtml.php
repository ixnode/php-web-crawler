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

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Value\Base\BaseValue;
use LogicException;

/**
 * Class XpathOuterHtml
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathOuterHtml extends BaseValue
{
    /**
     * Returns the outer html of the given dom node.
     *
     * @param DOMNode $domNode
     * @return string
     */
    private function getOuterHtml(DOMNode $domNode): string
    {
        $ownerDocument = $domNode->ownerDocument;

        if (!$ownerDocument instanceof DOMDocument) {
            throw new LogicException('Unexpected result from xpath query');
        }

        $outerHtml = $ownerDocument->saveHTML($domNode);

        if (!is_string($outerHtml)) {
            throw new LogicException('Unexpected result from xpath query');
        }

        return $outerHtml;
    }

    /**
     * Parses the given xpath.
     *
     * @inheritdoc
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|null
    {
        $domNodeList = $xpath->query($this->value, $node);

        if (!$domNodeList instanceof DOMNodeList) {
            throw new LogicException('Unexpected result from xpath query');
        }

        if ($domNodeList->length === 0) {
            return null;
        }

        if ($domNodeList->length === 1) {
            $domNode = $domNodeList->item(0);

            if (!$domNode instanceof DOMNode) {
                throw new LogicException('Unexpected result from xpath query');
            }

            return $this->applyChildren($this->getOuterHtml($domNode));
        }

        $data = [];
        foreach ($domNodeList as $domNode) {
            $data[] = $this->applyChildren($this->getOuterHtml($domNode));
        }

        return new Json($data);
    }
}
