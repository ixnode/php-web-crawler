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

use Ixnode\PhpWebCrawler\Converter\Scalar\DateParser;
use Ixnode\PhpWebCrawler\Converter\Scalar\PregReplace;
use Ixnode\PhpWebCrawler\Converter\Scalar\Trim;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Source\File;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$sourceFile = dirname(__DIR__).'/examples/html/converter.html';

$html = new File(
    $sourceFile,
    new Field(
        'version',
        new Text(
            '1.0'
        )
    ),
    new Field(
        'title',
        new XpathTextNode(
            '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/h1',
            new Trim()
        )
    ),
    new Field(
        'date',
        new XpathTextNode(
            '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/div[2]/a[4]',
            new Trim(),
            new PregReplace('~ \([^\(]+\)~', ''),
            new DateParser('d M Y H:i:s', '%s 12:00:00')
        )
    )
);

try {
    echo $html->parse()->getJsonStringFormatted() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
