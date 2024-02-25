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

use Ixnode\PhpWebCrawler\Converter\Collection\Implode;
use Ixnode\PhpWebCrawler\Converter\Collection\RemoveEmpty;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Source\Raw;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$rawHtml = <<<HTML
<html>
    <head>
        <title>Test Page</title>
    </head>
    <body>
        <h1>Test Title</h1>
        <p>Test Paragraph 1</p>
        <p></p>
        <p>Test Paragraph 2</p>
    </body>
</html>
HTML;

$html = new Raw(
    $rawHtml,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextNode('//h1')),
    new Field('paragraph', new XpathTextNode('//p', new RemoveEmpty(), new Implode()))
);

try {
    echo $html->parse()->getJsonStringFormatted().PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
