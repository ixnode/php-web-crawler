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

namespace Ixnode\PhpWebCrawler\Converter\Base;

use Ixnode\PhpContainer\Json;
use Ixnode\PhpWebCrawler\Source\Base\Source;

/**
 * Class BaseConverterArray
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-25)
 * @since 0.1.0 (2024-02-25) First version.
 */
abstract class BaseConverterArray implements ConverterArray
{
    protected Source $initiator;

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

        return $this;
    }

    /**
     * Returns the converted value.
     *
     * @param array<int, Json|bool|float|int|string|null>|bool|float|int|string|null $value
     * @return array<int, Json|bool|float|int|string|null>|bool|float|int|string|null
     */
    abstract public function getValue(array|bool|float|int|string|null $value): array|bool|float|int|string|null;
}
