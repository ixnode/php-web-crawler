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

/**
 * Class Boolean
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-25)
 * @since 0.1.0 (2024-02-25) First version.
 */
class Boolean extends BaseConverterScalar
{
    /**
     * Returns the converted value.
     *
     * @inheritdoc
     */
    public function getValue(bool|float|int|string|null $value): bool
    {
        return !empty($value);
    }
}

