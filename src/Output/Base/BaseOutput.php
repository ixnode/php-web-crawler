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

namespace Ixnode\PhpWebCrawler\Output\Base;

use DOMNode;
use DOMXPath;
use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Converter\Base\BaseConverter;
use Ixnode\PhpWebCrawler\Converter\Base\Converter;
use Ixnode\PhpWebCrawler\Source\Base\BaseSource;
use Ixnode\PhpWebCrawler\Source\Base\Source;
use Ixnode\PhpWebCrawler\Value\Base\BaseValue;
use JsonException;
use LogicException;

/**
 * Class BaseOutput
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BaseOutput implements Output
{
    protected Source $initiator;

    protected string|null $value = null;

    /** @var BaseValue[] $values */
    protected array $values = [];

    /** @var BaseOutput[] $outputs */
    protected array $outputs = [];

    /** @var BaseSource[] $sources */
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
                $parameter instanceof BaseConverter => $this->converters[] = $parameter,
                $parameter instanceof BaseOutput => $this->outputs[] = $parameter,
                $parameter instanceof BaseSource => $this->sources[] = $parameter,
                $parameter instanceof BaseValue => $this->values[] = $parameter,
                default => throw new LogicException(sprintf('Invalid parameter "%s"', gettype($parameter))),
            };
        }
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
        foreach ($this->converters as $converter) { $converter->setInitiator($initiator); }

        return $this;
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
