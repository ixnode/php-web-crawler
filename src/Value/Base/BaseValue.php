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
use Ixnode\PhpWebCrawler\Converter\Base\BaseConverter;
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
 */
abstract class BaseValue implements Value
{
    protected Source $initiator;

    protected string $value;

    /** @var BaseValue[] $values */
    protected array $values = [];

    /** @var BaseOutput[] $outputs */
    protected array $outputs = [];

    /** @var BaseConverter[] $converters */
    protected array $converters = [];

    /** @var BaseSource[] $sources */
    protected array $sources = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            match (true) {
                is_null($parameter) => $this->value = '',
                is_string($parameter) => $this->value = $parameter,
                $parameter instanceof BaseValue => $this->values[] = $parameter,
                $parameter instanceof BaseOutput => $this->outputs[] = $parameter,
                $parameter instanceof BaseConverter => $this->converters[] = $parameter,
                $parameter instanceof BaseSource => $this->sources[] = $parameter,
                default => throw new LogicException(sprintf('Parameter "%s" is not supported.', gettype($parameter)))
            };
        }

        if (!isset($this->value)) {
            throw new LogicException('$this->value is required.');
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
