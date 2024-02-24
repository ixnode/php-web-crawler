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
use Ixnode\PhpWebCrawler\Converter\Converter;
use Ixnode\PhpWebCrawler\Source\Source;
use Ixnode\PhpWebCrawler\Value\Value;
use JsonException;
use LogicException;

/**
 * Class Output
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Output
{
    protected string|null $value = null;

    /** @var Value[] $values */
    protected array $values = [];

    /** @var Output[] $outputs */
    protected array $outputs = [];

    /** @var Source[] $sources */
    protected array $sources = [];

    /** @var Converter[] $converters */
    protected array $converters = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            match (true) {
                is_string($parameter) => $this->value = $parameter,
                $parameter instanceof Value => $this->values[] = $parameter,
                $parameter instanceof Output => $this->outputs[] = $parameter,
                $parameter instanceof Source => $this->sources[] = $parameter,
                $parameter instanceof Converter => $this->converters[] = $parameter,
                default => throw new LogicException(sprintf('Invalid parameter "%s"', gettype($parameter))),
            };
        }
    }

    /**
     * Returns the name of the output.
     *
     * @return string|null
     */
    public function getValue(): string|null
    {
        return $this->value;
    }

    /**
     * Returns the structured data.
     *
     * @param Json|string|int|null $data
     * @return Json|string|int|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    protected function getStructuredData(Json|string|int|null $data): Json|string|int|null
    {
        if (is_null($this->value)) {
            return $data;
        }

        $data = match (true) {
            $data instanceof Json => $data->getArray(),
            default => $data,
        };

        return new Json([$this->getValue() => $data]);
    }

    /**
     * Parses the given xpath.
     *
     * @param DOMXPath $xpath
     * @param DOMNode|null $node
     * @return Json|string|int|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    abstract public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|null;
}
