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
use Ixnode\PhpWebCrawler\Converter\Collection\Concat;
use Ixnode\PhpWebCrawler\Converter\Scalar\Number;
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
        <p>3,600</p>
        <p>Test Paragraph 3</p>
        
        <p>Test Paragraph 4</p>
        <p>1,800</p>
        <p>Test Paragraph 6</p>
        
        <p>Test Paragraph 7</p>
        <p>300</p>
        <p>Test Paragraph 9</p>
    </body>
</html>
HTML;

$html = new Raw(
    $rawHtml,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextNode('//h1')),
    new Field('paragraph', new XpathTextNode('//p', new Concat(3, ', ', [null, new Number([',', '.'], ''), null])))
);

try {
    echo $html->parse()->getJsonStringFormatted().PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
