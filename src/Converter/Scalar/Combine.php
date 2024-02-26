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

namespace Ixnode\PhpWebCrawler\Converter\Scalar;

use Ixnode\PhpWebCrawler\Converter\Scalar\Base\BaseConverterScalar;
use LogicException;

/**
 * Class Combine
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-26)
 * @since 0.1.0 (2024-02-26) First version.
 */
class Combine extends BaseConverterScalar
{
    /** @var array<int, BaseConverterScalar> $scalarConverters */
    private array $scalarConverters = [];

    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            match (true) {
                $parameter instanceof BaseConverterScalar => $this->scalarConverters[] = $parameter,
                default => throw new LogicException(sprintf('Invalid parameter "%s"', gettype($parameter))),
            };
        }
    }

    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(bool|float|int|string|null $value): bool|float|int|string|null
    {
        foreach ($this->scalarConverters as $scalarConverter) {
            $value = $scalarConverter->getValue($value);
        }

        return $value;
    }
}

