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
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Output\Base\BaseOutput;
use JsonException;
use LogicException;

/**
 * Class Group
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 */
class Group extends BaseOutput
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
    protected function doParse(DOMXPath $xpath, DOMNode $node = null): Json
    {
        $data = [];

        foreach ($this->outputs as $output) {
            $parsed = $output->parse($xpath, $node);

            if (!$parsed instanceof Json) {
                throw new LogicException('Unexpected data type.');
            }

            $data = array_merge_recursive($data, $parsed->getArray());
        }

        foreach ($this->sources as $source) {
            $parsed = $source->parse($xpath, $node);

            if (!$parsed instanceof Json) {
                throw new LogicException('Unexpected data type.');
            }

            $data = array_merge_recursive($data, $parsed->getArray());
        }

        return new Json($data);
    }

    /**
     * Parses the given xpath.
     *
     * @inheritdoc
     */
    public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|float|bool|null
    {
        return $this->getStructuredData($this->doParse($xpath, $node));
    }
}
