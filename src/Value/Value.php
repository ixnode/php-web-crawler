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

use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Converter\Converter;
use Ixnode\PhpWebCrawler\Output\Output;
use DOMXPath;
use DOMNode;
use Ixnode\PhpWebCrawler\Source\Source;
use JsonException;
use LogicException;
use Stringable;

/**
 * Class Value
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Value implements Stringable
{
    protected string $value;

    /** @var Value[] $values */
    protected array $values = [];

    /** @var Output[] $outputs */
    protected array $outputs = [];

    /** @var Converter[] $converters */
    protected array $converters = [];

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
                is_string($parameter) => $this->value = $parameter,
                $parameter instanceof Value => $this->values[] = $parameter,
                $parameter instanceof Output => $this->outputs[] = $parameter,
                $parameter instanceof Converter => $this->converters[] = $parameter,
                $parameter instanceof Source => $this->sources[] = $parameter,
                default => throw new LogicException(sprintf('Parameter "%s" is not supported.', gettype($parameter)))
            };
        }

        if (!isset($this->value)) {
            throw new LogicException('$this->value is required.');
        }
    }

    /**
     * To string method.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Json|string|int|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    protected function applyChildren(string $value): Json|string|int|null
    {
        /* Apply all filters to value. */
        foreach ($this->converters as $converter) {
            $value = $converter->getValue($value);
        }

        if (count($this->sources) <= 0) {
            return $value;
        }

        $data = [];

        foreach ($this->sources as $source) {
            $source->__construct($value);

            $data = array_merge_recursive($data, $source->parse()->getArray());
        }

        return new Json($data);
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
