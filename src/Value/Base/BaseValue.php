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

namespace Ixnode\PhpWebCrawler\Value\Base;

use DOMNode;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Converter\Collection\Base\BaseConverterArray;
use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverterScalar;
use Ixnode\PhpWebCrawler\Output\Base\BaseOutput;
use Ixnode\PhpWebCrawler\Source\Base\BaseSource;
use Ixnode\PhpWebCrawler\Source\Base\Source;
use JsonException;
use LogicException;

/**
 * Class BaseValue
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class BaseValue implements Value
{
    protected Source $initiator;

    protected string|int|float|bool|null $value = null;

    /** @var BaseValue[] $values */
    protected array $values = [];

    /** @var BaseOutput[] $outputs */
    protected array $outputs = [];

    /** @var BaseConverterScalar[] $scalarConverters */
    protected array $scalarConverters = [];

    /** @var BaseConverterScalar[] $scalarAfterArrayConverters */
    protected array $scalarAfterArrayConverters = [];

    /** @var BaseConverterArray[] $arrayConverters */
    protected array $arrayConverters = [];

    /** @var BaseSource[] $sources */
    protected array $sources = [];

    private const ZERO_NUMBER = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            match (true) {
                /* Scalar values. */
                is_string($parameter),
                is_int($parameter),
                is_float($parameter),
                is_bool($parameter),
                is_null($parameter) => $this->value = $parameter,

                $parameter instanceof BaseValue => $this->values[] = $parameter,

                $parameter instanceof BaseOutput => $this->outputs[] = $parameter,

                $parameter instanceof BaseConverterScalar && count($this->arrayConverters) <= self::ZERO_NUMBER =>
                    $this->scalarConverters[] = $parameter,
                $parameter instanceof BaseConverterScalar =>
                    $this->scalarAfterArrayConverters[] = $parameter,

                $parameter instanceof BaseConverterArray => $this->arrayConverters[] = $parameter,

                $parameter instanceof BaseSource => $this->sources[] = $parameter,

                default => throw new LogicException(sprintf('Parameter "%s" is not supported.', gettype($parameter)))
            };
        }
    }

    /**
     * @return string|int|float|bool|null
     */
    public function getValue(): string|int|float|bool|null
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function getInitiator(): Source
    {
        return $this->initiator;
    }

    /**
     * @inheritdoc
     */
    public function setInitiator(Source $initiator): self
    {
        $this->initiator = $initiator;

        foreach ($this->values as $value) { $value->setInitiator($initiator); }
        foreach ($this->outputs as $output) { $output->setInitiator($initiator); }
        foreach ($this->sources as $source) { $source->setInitiator($initiator); }
        foreach ($this->scalarConverters as $scalarConverter) { $scalarConverter->setInitiator($initiator); }
        foreach ($this->scalarAfterArrayConverters as $scalarAfterArrayConverter) { $scalarAfterArrayConverter->setInitiator($initiator); }
        foreach ($this->arrayConverters as $arrayConverter) { $arrayConverter->setInitiator($initiator); }

        return $this;
    }

    /**
     * @param string|int|float|bool|null $value
     * @param bool $applyAfter
     * @return Json|string|int|float|bool|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function applyChildren(string|int|float|bool|null $value, bool $applyAfter = false): Json|string|int|float|bool|null
    {
        /* Apply all filters to value. */
        foreach ($this->scalarConverters as $converter) {
            $value = $converter->getValue($value);
        }

        if ($applyAfter) {
            $value = $this->applyChildrenAfter($value);
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
     * @param string|int|float|bool|null $value
     * @return Json|string|int|float|bool|null
     */
    protected function applyChildrenAfter(string|int|float|bool|null $value): Json|string|int|float|bool|null
    {
        foreach ($this->scalarAfterArrayConverters as $scalarAfterArrayConverter) {
            $value = $scalarAfterArrayConverter->getValue($value);
        }

        return $value;
    }

    /**
     * @param array<int, Json|string|int|float|bool|null>|bool|float|int|string|null $value
     * @return Json|bool|float|int|string|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    protected function applyChildrenArray(array|bool|float|int|string|null $value): Json|string|int|float|bool|null
    {
        /* Apply all filters to value. */
        foreach ($this->arrayConverters as $arrayConverter) {
            $value = $arrayConverter->getValue($value);
        }

        if (!is_array($value)) {
            return $this->applyChildrenAfter($value);
        }

        return new Json($value);
    }

    /**
     * Parses the given xpath.
     *
     * @param DOMXPath $xpath
     * @param DOMNode|null $node
     * @return Json|string|int|float|bool|null
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    abstract public function parse(DOMXPath $xpath, DOMNode $node = null): Json|string|int|float|bool|null;
}
