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

use DOMDocument;
use DOMNode;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Output\Output;
use Ixnode\PhpWebCrawler\Value\Value;
use JsonException;
use LogicException;

/**
 * Class Source
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Source
{
    protected string|null $source = null;

    /** @var Output[]|Field[] $outputs */
    protected array $outputs = [];

    /** @var Source[] $sources */
    protected array $sources = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            match (true) {
                /* Main config. */
                is_string($parameter) => $this->addSource($parameter),

                /* add Output object. */
                $parameter instanceof Output => $this->outputs[] = $parameter,

                /* convert Value object to Output object. */
                $parameter instanceof Value => $this->outputs[] = new Field($parameter),

                /* Adds the source object. */
                $parameter instanceof Source => $this->sources[] = $parameter,

                /* Unknown parameter. */
                default => throw new LogicException(sprintf('Parameter "%s" is not supported.', gettype($parameter))),
            };
        }
    }

    /**
     * Returns the DOMXPath object.
     *
     * @return DOMXPath
     */
    protected function getDOMXPathFromSource(): DOMXPath
    {
        if (is_null($this->source)) {
            throw new LogicException('Source is not set');
        }

        $doc = new DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($this->source);
        libxml_clear_errors();

        return new DOMXPath($doc);
    }

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
    public function parse(DOMXPath $xpath = null, DOMNode $node = null): Json
    {
        $xpath = $this->getDOMXPathFromSource();

        return $this->doParse($xpath, $node);
    }

    /**
     * Adds the source to this object.
     *
     * @param string $source
     * @return void
     */
    abstract public function addSource(string $source): void;
}
