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

namespace Ixnode\PhpWebCrawler\Source\Base;

/**
 * Interface Source
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-25)
 * @since 0.1.0 (2024-02-25) First version.
 */
interface Source
{
    /**
     * @return Source|null
     */
    public function getInitiator(): ?Source;

    /**
     * @param Source $initiator
     * @return self
     */
    public function setInitiator(Source $initiator): self;
}

