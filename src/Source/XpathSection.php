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
 * Class XpathSection
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathSection extends Source
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

    /**
     * Parses the given xpath.
     *
     * @param DOMXPath|null $xpath
     * @param DOMNode|null $node
     * @return Json
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws TypeInvalidException
     * @throws FunctionReplaceException
     * @throws JsonException
     */
    public function parse(DOMXPath|null $xpath = null, DOMNode $node = null): Json
    {
        if (is_null($xpath)) {
            throw new LogicException('Xpath is not set');
        }

        if (is_null($this->source)) {
            throw new LogicException('Source is not set');
        }

        $nodeList = $xpath->query($this->source, $node);

        if (!$nodeList instanceof DOMNodeList) {
            throw new LogicException('Unexpected result from xpath query');
        }

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
        }

        return $this->doParse($xpath, $node);
    }
}
