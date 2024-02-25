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
 * Class XpathInnerHtml
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathInnerHtml extends BaseValue
{
    /**
     * Returns the inner html of the given dom node.
     *
     * @param DOMNode $domNode
     * @return string
     */
    private function getInnerHtml(DOMNode $domNode): string
    {
        $innerHtml = '';

        foreach ($domNode->childNodes as $child) {
            $ownerDocument = $domNode->ownerDocument;

            if (!$ownerDocument instanceof DOMDocument) {
                throw new LogicException('Unexpected result from xpath query');
            }

            $html = $ownerDocument->saveHTML($child);

            if (!is_string($html)) {
                throw new LogicException('Unexpected result from xpath query');
            }

            $innerHtml .= $html;
        }

        return $innerHtml;
    }

    /**
     * Parses the given xpath.
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
            return null;
        }

        if ($domNodeList->length === 1) {
            $domNode = $domNodeList->item(0);

            if (!$domNode instanceof DOMNode) {
                throw new LogicException('Unexpected result from xpath query');
            }

            return $this->applyChildren($this->getInnerHtml($domNode));
        }

        $data = [];
        foreach ($domNodeList as $domNode) {
            $data[] = $this->applyChildren($this->getInnerHtml($domNode));
        }

        return new Json($data);
    }
}
