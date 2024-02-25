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

use DOMNode;
use DOMNodeList;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Source\Base\BaseSource;
use JsonException;
use LogicException;

/**
 * Class XpathSections
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class XpathSections extends BaseSource
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
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    public function parse(DOMXPath|null $xpath = null, DOMNode $node = null): Json
    {
        if (is_null($xpath)) {
            throw new LogicException('Xpath is not set');
        }

        if (is_null($this->source)) {
            throw new LogicException('Source is not set');
        }

        $collectedData = [];

        $nodeList = $xpath->query($this->source, $node);

        if (!$nodeList instanceof DOMNodeList) {
            throw new LogicException('Unexpected result from xpath query');
        }

        foreach ($nodeList as $node) {
            $collectedData[] = $this->doParse($xpath, $node)->getArray();
        }

        return new Json($collectedData);
    }
}