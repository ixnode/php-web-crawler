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

use DOMNodeList;
use DOMXPath;
use DOMNode;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use JsonException;
use LogicException;

/**
 * Class XpathTextnodes
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathTextNodes extends Value
{
    /**
     * Parses the given xpath.
     *
     * @param DOMXPath $xpath
     * @param DOMNode|null $node
     * @return Json
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws TypeInvalidException
     * @throws FunctionReplaceException
     * @throws JsonException
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json
    {
        $domNodeList = $xpath->query($this->value, $node);

        if (!$domNodeList instanceof DOMNodeList) {
            throw new LogicException('Unexpected result from xpath query');
        }

        if ($domNodeList->length === 0) {
            return new Json([]);
        }

        $data = [];

        foreach ($domNodeList as $domNode) {
            $parsed = $this->applyChildren($domNode->textContent);

            $data[] = match (true) {
                $parsed instanceof Json => $parsed->getArray(),
                default => $parsed,
            };
        }

        return new Json($data);
    }
}
