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

require dirname(__DIR__).'/vendor/autoload.php';

use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Output\Group;
use Ixnode\PhpWebCrawler\Source\File;
use Ixnode\PhpWebCrawler\Source\XpathSections;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$sourceFile = dirname(__DIR__).'/examples/html/search.html';

$html = new File(
    $sourceFile,
    new Field('title', new XpathTextNode('//*[@id="firstHeading"]')),
    new Group(
        'hits',
        new XpathSections(
            '//*[@id="mw-content-text"]/div/ul/li',
            new Field('title', new XpathTextNode('./div[1]/a')),
            new Field('link', new XpathTextNode('./div[1]/a/@href'))
        )
    )
);

try {
    echo $html->parse()->getJsonStringFormatted() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
